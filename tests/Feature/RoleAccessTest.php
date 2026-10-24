<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_non_admin_role_is_forbidden_from_restricted_modules(): void
    {
        $rolesAndDeniedRoutes = [
            User::ROLE_ASSISTANT => [
                'admin.users.index',
                'productos.create',
                'entradas.create',
                'salidas.create',
                'mecanico.citas.index',
                'mis-citas.index',
            ],
            User::ROLE_STOREKEEPER => [
                'admin.users.index',
                'admin.clientes.index',
                'admin.motos.index',
                'admin.citas.index',
                'mecanico.citas.index',
                'mis-citas.index',
            ],
            User::ROLE_MECHANIC => [
                'admin.users.index',
                'admin.clientes.index',
                'admin.motos.index',
                'admin.citas.index',
                'productos.index',
                'inventario.index',
                'mis-citas.index',
            ],
            User::ROLE_CLIENT => [
                'admin.users.index',
                'admin.clientes.index',
                'admin.motos.index',
                'admin.citas.index',
                'productos.index',
                'inventario.index',
                'mecanico.citas.index',
            ],
        ];

        foreach ($rolesAndDeniedRoutes as $role => $routes) {
            $user = User::factory()->create(['role' => $role]);

            foreach ($routes as $routeName) {
                $this->actingAs($user)
                    ->get(route($routeName))
                    ->assertForbidden();
            }
        }
    }

    public function test_assistant_and_storekeeper_get_only_their_allowed_read_and_write_routes(): void
    {
        $assistant = User::factory()->create(['role' => User::ROLE_ASSISTANT]);
        $this->actingAs($assistant)->get(route('dashboard'))->assertOk();
        $this->actingAs($assistant)
            ->get(route('admin.clientes.index'))->assertOk();
        $this->get(route('admin.motos.index'))->assertOk();
        $this->get(route('admin.citas.index'))->assertOk();
        $this->get(route('productos.index'))->assertOk();
        $this->get(route('entradas.index'))->assertOk();
        $this->get(route('salidas.index'))->assertOk();

        $storekeeper = User::factory()->create(['role' => User::ROLE_STOREKEEPER]);
        $this->actingAs($storekeeper)->get(route('dashboard'))->assertOk();
        $this->actingAs($storekeeper)
            ->get(route('productos.create'))->assertOk();
        $this->get(route('entradas.create'))->assertOk();
        $this->get(route('salidas.create'))->assertOk();
        $this->get(route('inventario.index'))->assertOk();
    }

    public function test_mechanic_only_sees_and_attends_assigned_appointments(): void
    {
        $mechanic = User::factory()->create(['role' => User::ROLE_MECHANIC]);
        $otherMechanic = User::factory()->create(['role' => User::ROLE_MECHANIC]);
        $ownCita = $this->createCita('Servicio asignado', $mechanic);
        $otherCita = $this->createCita('Servicio ajeno', $otherMechanic);

        $this->actingAs($mechanic)
            ->get(route('mecanico.citas.index'))
            ->assertOk()
            ->assertSee('Servicio asignado')
            ->assertDontSee('Servicio ajeno');

        $this->patch(route('mecanico.citas.atender', $otherCita))->assertNotFound();
        $this->patch(route('mecanico.citas.atender', $ownCita))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('citas', ['id' => $ownCita->id, 'estado' => 'atendida']);
        $this->assertDatabaseHas('citas', ['id' => $otherCita->id, 'estado' => 'solicitada']);
    }

    public function test_client_only_sees_owns_appointments_and_motos_and_cannot_cancel_another_clients(): void
    {
        [$user, $cliente] = $this->createClientAccount();
        [, $otherCliente] = $this->createClientAccount('otra.cliente@motogestor.com');
        $ownMoto = Moto::factory()->create(['cliente_id' => $cliente->id]);
        $otherMoto = Moto::factory()->create(['cliente_id' => $otherCliente->id]);
        $ownCita = $this->createCita('Servicio propio', null, $cliente, $ownMoto);
        $otherCita = $this->createCita('Servicio de otro', null, $otherCliente, $otherMoto);

        $this->actingAs($user)
            ->get(route('mis-citas.index'))
            ->assertOk()
            ->assertSee('Servicio propio')
            ->assertDontSee('Servicio de otro');

        $this->get(route('mis-motos.index'))
            ->assertOk()
            ->assertSee($ownMoto->placa)
            ->assertDontSee($otherMoto->placa);

        $this->get(route('api.clientes.motos', $cliente))
            ->assertOk()
            ->assertJsonFragment(['placa' => $ownMoto->placa])
            ->assertJsonMissing(['placa' => $otherMoto->placa]);
        $this->get(route('api.clientes.motos', $otherCliente))->assertNotFound();

        $this->patch(route('mis-citas.cancelar', $otherCita), [
            'motivo_cancelacion' => 'No corresponde a mi cuenta',
        ])->assertNotFound();

        $this->patch(route('mis-citas.cancelar', $ownCita), [
            'motivo_cancelacion' => 'No puedo asistir',
        ])->assertRedirect();

        $this->assertDatabaseHas('citas', [
            'id' => $ownCita->id,
            'estado' => 'cancelada',
            'motivo_cancelacion' => 'No puedo asistir',
        ]);
        $this->assertDatabaseHas('citas', ['id' => $otherCita->id, 'estado' => 'solicitada']);
    }

    public function test_client_appointment_request_uses_linked_client_even_if_another_id_is_submitted(): void
    {
        [$user, $cliente] = $this->createClientAccount();
        [, $otherCliente] = $this->createClientAccount('otra.cuenta@motogestor.com');
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        $this->actingAs($user)
            ->post(route('mis-citas.store'), [
                'cliente_id' => $otherCliente->id,
                'moto_id' => $moto->id,
                'fecha' => '2026-12-12',
                'hora' => '10:00',
                'servicio' => 'Servicio solicitado',
            ])
            ->assertRedirect(route('mis-citas.index'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'estado' => 'solicitada',
        ]);
    }

    public function test_inactive_user_is_logged_out_before_accessing_role_routes(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ASSISTANT,
            'status' => 'inactivo',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_can_create_a_client_user_and_link_exactly_one_client_record(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $cliente = Cliente::factory()->create(['user_id' => null]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Cuenta Cliente',
                'email' => 'cuenta.cliente@motogestor.com',
                'password' => 'Motogestor2026!',
                'password_confirmation' => 'Motogestor2026!',
                'role' => User::ROLE_CLIENT,
                'status' => 'activo',
                'cliente_id' => $cliente->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'cuenta.cliente@motogestor.com')->firstOrFail();
        $this->assertSame($user->id, $cliente->fresh()->user_id);
        $this->assertSame($cliente->id, $user->cliente->id);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['status' => 'activo']);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggleStatus', $user))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'inactivo']);
    }

    public function test_admin_can_access_routes_available_to_other_roles(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('mis-citas.index'))->assertOk();
        $this->get(route('mecanico.citas.index'))->assertOk();
        $this->get(route('productos.create'))->assertOk();
        $this->get(route('entradas.create'))->assertOk();
        $this->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Al menos 8 caracteres')
        ->assertSee('data-password-toggle="password"', false);

        $user = User::factory()->create();
        $this->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee('Al menos 8 caracteres')
        ->assertSee('data-password-toggle="password_confirmation"', false);
    }

    private function createClientAccount(string $email = 'cliente@motogestor.com'): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_CLIENT,
            'email' => $email,
        ]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        return [$user, $cliente];
    }

    private function createCita(
        string $service,
        ?User $mechanic,
        ?Cliente $cliente = null,
        ?Moto $moto = null
    ): Cita {
        $cliente ??= Cliente::factory()->create();
        $moto ??= Moto::factory()->create(['cliente_id' => $cliente->id]);

        return Cita::factory()->create([
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => $mechanic?->id,
            'servicio' => $service,
            'estado' => 'solicitada',
        ]);
    }
}
