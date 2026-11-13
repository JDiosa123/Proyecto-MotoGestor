<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RejectCitaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:15:00', 'America/Bogota'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_assistant_rejects_a_requested_appointment_and_saves_the_reason(): void
    {
        $assistant = User::factory()->asistente()->create();
        $cita = $this->createCita();

        $this->actingAs($assistant)
            ->patch(route('admin.citas.reject', $cita), [
                'motivo_cancelacion' => 'No hay disponibilidad para el servicio.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Cita rechazada correctamente.');

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'cancelada',
            'motivo_cancelacion' => 'No hay disponibilidad para el servicio.',
        ]);
    }

    public function test_reject_requires_a_reason_between_five_and_255_characters(): void
    {
        $assistant = User::factory()->asistente()->create();
        $cita = $this->createCita();

        foreach ([
            [],
            ['motivo_cancelacion' => 'No'],
            ['motivo_cancelacion' => str_repeat('a', 256)],
        ] as $data) {
            $this->actingAs($assistant)
                ->from(route('admin.citas.index'))
                ->patch(route('admin.citas.reject', $cita), $data)
                ->assertRedirect(route('admin.citas.index'))
                ->assertSessionHasErrors('motivo_cancelacion');

            $this->assertDatabaseHas('citas', [
                'id' => $cita->id,
                'estado' => 'solicitada',
                'motivo_cancelacion' => null,
            ]);
        }
    }

    public function test_rejecting_an_appointment_not_requested_returns_422_without_changes(): void
    {
        $assistant = User::factory()->asistente()->create();

        foreach (['aceptada', 'atendida', 'cancelada'] as $estado) {
            $cita = $this->createCita([
                'estado' => $estado,
                'motivo_cancelacion' => $estado === 'cancelada' ? 'Cancelación previa' : null,
            ]);

            $this->actingAs($assistant)
                ->patch(route('admin.citas.reject', $cita), [
                    'motivo_cancelacion' => 'Motivo válido de rechazo',
                ])
                ->assertUnprocessable();

            $this->assertDatabaseHas('citas', [
                'id' => $cita->id,
                'estado' => $estado,
                'motivo_cancelacion' => $estado === 'cancelada' ? 'Cancelación previa' : null,
            ]);
        }
    }

    public function test_only_admin_and_assistant_can_reject_an_appointment(): void
    {
        $cita = $this->createCita();

        foreach ([
            User::ROLE_STOREKEEPER,
            User::ROLE_MECHANIC,
            User::ROLE_CLIENT,
        ] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->patch(route('admin.citas.reject', $cita), [
                    'motivo_cancelacion' => 'Motivo válido',
                ])
                ->assertForbidden();

            $this->assertDatabaseHas('citas', [
                'id' => $cita->id,
                'estado' => 'solicitada',
                'motivo_cancelacion' => null,
            ]);
        }
    }

    public function test_rejected_appointment_releases_its_date_time_and_mechanic(): void
    {
        $assistant = User::factory()->asistente()->create();
        $mechanic = User::factory()->mecanico()->create();
        $cita = $this->createCita([
            'mecanico_id' => $mechanic->id,
            'fecha' => $this->nextFriday(),
            'hora' => '10:00:00',
        ]);

        $this->actingAs($assistant)->patch(route('admin.citas.reject', $cita), [
            'motivo_cancelacion' => 'No hay disponibilidad.',
        ])->assertRedirect();

        $otherCliente = Cliente::factory()->create();
        $otherMoto = Moto::factory()->create(['cliente_id' => $otherCliente->id]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.citas.store'), [
                'cliente_id' => $otherCliente->id,
                'moto_id' => $otherMoto->id,
                'mecanico_id' => $mechanic->id,
                'fecha' => $this->nextFriday(),
                'hora' => '10:00',
                'servicio' => 'Cambio de aceite',
            ])
            ->assertRedirect(route('admin.citas.index'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $otherCliente->id,
            'mecanico_id' => $mechanic->id,
            'fecha' => $this->nextFriday().' 00:00:00',
            'hora' => '10:00',
            'estado' => 'solicitada',
        ]);
    }

    public function test_canceled_appointment_does_not_block_an_edit_to_its_time_slot(): void
    {
        $assistant = User::factory()->asistente()->create();
        $mechanic = User::factory()->mecanico()->create();
        $cita = $this->createCita([
            'mecanico_id' => $mechanic->id,
            'fecha' => $this->nextFriday(),
            'hora' => '10:00:00',
        ]);

        $this->actingAs($assistant)->patch(route('admin.citas.reject', $cita), [
            'motivo_cancelacion' => 'No hay disponibilidad.',
        ])->assertRedirect();

        $target = $this->createCita([
            'fecha' => Carbon::parse($this->nextFriday())->addDay()->toDateString(),
            'hora' => '11:00:00',
        ]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.citas.update', $target), [
                'cliente_id' => $target->cliente_id,
                'moto_id' => $target->moto_id,
                'mecanico_id' => $mechanic->id,
                'fecha' => $this->nextFriday(),
                'hora' => '10:00',
                'estado' => 'solicitada',
                'servicio' => $target->servicio,
                'motivo_cancelacion' => null,
            ])
            ->assertRedirect(route('admin.citas.index'));

        $this->assertSame($this->nextFriday(), $target->fresh()->fecha->format('Y-m-d'));
        $this->assertSame($mechanic->id, $target->fresh()->mecanico_id);
    }

    public function test_reject_button_is_limited_to_admin_and_assistant_requested_appointments(): void
    {
        $requested = $this->createCita();
        $accepted = $this->createCita(['estado' => 'aceptada']);
        $assistant = User::factory()->asistente()->create();

        $this->actingAs($assistant)
            ->get(route('admin.citas.index'))
            ->assertOk()
            ->assertSee(route('admin.citas.reject', $requested), false)
            ->assertDontSee(route('admin.citas.reject', $accepted), false);

        [$clientUser, $client] = $this->createClientAccount();
        $this->createCita(['cliente_id' => $client->id]);

        $this->actingAs($clientUser)
            ->get(route('mis-citas.index'))
            ->assertOk()
            ->assertDontSee('Rechazar');
    }

    public function test_client_sees_full_cancellation_reason_and_only_own_appointments(): void
    {
        [$user, $cliente] = $this->createClientAccount();
        [, $otherCliente] = $this->createClientAccount('otro-cliente@example.com');
        $cita = $this->createCita([
            'cliente_id' => $cliente->id,
            'estado' => 'cancelada',
            'motivo_cancelacion' => 'El taller no cuenta con repuestos para esta reparación.',
            'servicio' => 'Servicio propio',
        ]);
        $otherCita = $this->createCita([
            'cliente_id' => $otherCliente->id,
            'estado' => 'solicitada',
            'servicio' => 'Servicio de otro cliente',
        ]);

        $this->actingAs($user)
            ->get(route('mis-citas.index'))
            ->assertOk()
            ->assertSee('Cancelada')
            ->assertSee('El taller no cuenta con repuestos para esta reparación.')
            ->assertSee('Servicio propio')
            ->assertDontSee('Servicio de otro cliente');

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'cliente_id' => $cliente->id]);
        $this->assertDatabaseHas('citas', ['id' => $otherCita->id, 'cliente_id' => $otherCliente->id]);
    }

    private function createClientAccount(string $email = 'cliente-rechazo@example.com'): array
    {
        $user = User::factory()->cliente()->create(['email' => $email]);
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);

        return [$user, $cliente];
    }

    private function nextFriday(): string
    {
        return now(config('app.timezone'))->next('Friday')->toDateString();
    }

    private function createCita(array $attributes = []): Cita
    {
        $cliente = isset($attributes['cliente_id'])
            ? Cliente::findOrFail($attributes['cliente_id'])
            : Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        return Cita::factory()->create(array_merge([
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => null,
            'fecha' => $this->nextFriday(),
            'hora' => '10:00:00',
            'estado' => 'solicitada',
            'motivo_cancelacion' => null,
        ], $attributes, ['moto_id' => $moto->id]));
    }
}
