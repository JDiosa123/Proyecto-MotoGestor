<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCitaMotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_create_form_shows_only_own_motos_and_selects_the_only_moto(): void
    {
        [$user, $cliente] = $this->createClientAccount();
        [, $otherCliente] = $this->createClientAccount('otro-cliente@example.com');
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);
        $otherMoto = Moto::factory()->create(['cliente_id' => $otherCliente->id]);

        $this->actingAs($user)
            ->get(route('mis-citas.create'))
            ->assertOk()
            ->assertSee($moto->placa)
            ->assertSee('value="'.$moto->id.'" selected', false)
            ->assertDontSee($otherMoto->placa)
            ->assertDontSee('Seleccione primero un cliente')
            ->assertDontSee('fetch(`/api/clientes/');
    }

    public function test_customer_can_create_an_appointment_with_their_motorcycle(): void
    {
        [$user, $cliente] = $this->createClientAccount();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);
        $otherCliente = Cliente::factory()->create();

        $this->actingAs($user)
            ->post(route('mis-citas.store'), [
                'cliente_id' => $otherCliente->id,
                'moto_id' => $moto->id,
                'fecha' => '2026-12-12',
                'hora' => '10:00',
                'servicio' => 'Cambio de aceite',
            ])
            ->assertRedirect(route('mis-citas.index'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'estado' => 'solicitada',
            'servicio' => 'Cambio de aceite',
        ]);
        $this->assertDatabaseMissing('citas', ['cliente_id' => $otherCliente->id]);
    }

    public function test_customer_cannot_create_an_appointment_with_another_clients_motorcycle(): void
    {
        [$user] = $this->createClientAccount();
        [, $otherCliente] = $this->createClientAccount('otro-cliente@example.com');
        $otherMoto = Moto::factory()->create(['cliente_id' => $otherCliente->id]);

        $this->actingAs($user)
            ->from(route('mis-citas.create'))
            ->post(route('mis-citas.store'), [
                'cliente_id' => $otherCliente->id,
                'moto_id' => $otherMoto->id,
                'fecha' => '2026-12-12',
                'hora' => '10:00',
                'servicio' => 'Cambio de aceite',
            ])
            ->assertRedirect(route('mis-citas.create'))
            ->assertSessionHasErrors('moto_id');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_customer_without_motorcycles_sees_message_and_cannot_submit(): void
    {
        [$user] = $this->createClientAccount();

        $response = $this->actingAs($user)
            ->get(route('mis-citas.create'))
            ->assertOk()
            ->assertSee('No tienes motos registradas. Pide al taller que registre tu moto')
            ->assertSee('Agendar Cita');

        $this->assertMatchesRegularExpression(
            '/<button\b(?=[^>]*\bdisabled\b)[^>]*>[\s\S]*?Agendar Cita[\s\S]*?<\/button>/',
            $response->getContent()
        );

        $this->post(route('mis-citas.store'), [
            'fecha' => '2026-12-12',
            'hora' => '10:00',
            'servicio' => 'Cambio de aceite',
        ])->assertSessionHasErrors('moto_id');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_admin_and_assistant_can_still_choose_clients_and_motorcycles(): void
    {
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        foreach ([
            User::factory()->admin()->create(),
            User::factory()->asistente()->create(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('admin.citas.create'))
                ->assertOk()
                ->assertSee('value="'.$cliente->id.'"', false)
                ->assertSee('Seleccione primero un cliente')
                ->assertDontSee($moto->placa);

            $this->get(route('api.clientes.motos', $cliente))
                ->assertOk()
                ->assertJsonFragment(['placa' => $moto->placa]);
        }
    }

    private function createClientAccount(string $email = 'cliente-moto@example.com'): array
    {
        $user = User::factory()->cliente()->create(['email' => $email]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        return [$user, $cliente];
    }
}
