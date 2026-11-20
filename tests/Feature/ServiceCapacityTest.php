<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\Servicio;
use App\Models\User;
use App\Services\HorarioTaller;
use Database\Seeders\DiasNoLaborablesSeeder;
use Database\Seeders\ServiciosSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServiceCapacityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-13 10:15:00', 'America/Bogota'));
        $this->seed(DiasNoLaborablesSeeder::class);
        $this->seed(ServiciosSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_next_business_day_skips_saturday_sunday_and_holiday_for_full_day_service(): void
    {
        $this->assertSame('2026-11-17', app(HorarioTaller::class)->siguienteDiaHabil('2026-11-13'));
        $this->assertFalse(config('taller.sabado_dia_completo'));
    }

    public function test_daily_capacity_uses_the_active_mechanic_count_and_configured_limit(): void
    {
        $horario = app(HorarioTaller::class);
        $fecha = '2026-11-17';

        User::factory()->mecanico()->count(3)->create();
        $this->assertSame(3, $horario->cupoDelDia($fecha));

        User::where('role', User::ROLE_MECHANIC)->first()->update(['status' => 'inactivo']);
        $this->assertSame(2, $horario->cupoDelDia($fecha));

        User::where('role', User::ROLE_MECHANIC)->update(['status' => 'inactivo']);
        $this->assertSame(3, $horario->cupoDelDia($fecha));
    }

    public function test_canceled_and_requested_appointments_do_not_consume_full_day_capacity(): void
    {
        User::factory()->mecanico()->count(3)->create();
        $servicio = $this->mantenimiento();

        $this->createCita($servicio, ['estado' => 'aceptada']);
        $this->createCita($servicio, ['estado' => 'atendida']);
        $this->createCita($servicio, ['estado' => 'cancelada']);
        $this->createCita($servicio, ['estado' => 'solicitada']);

        $horario = app(HorarioTaller::class);
        $this->assertSame(2, $horario->cuposOcupados('2026-11-17'));
        $this->assertSame(1, $horario->cuposDisponibles('2026-11-17'));
    }

    public function test_fourth_maintenance_cannot_be_accepted_when_daily_capacity_is_full(): void
    {
        User::factory()->admin()->create();
        User::factory()->mecanico()->count(3)->create();
        $servicio = $this->mantenimiento();
        $citas = collect(range(1, 4))->map(fn () => $this->createCita($servicio));

        foreach ($citas->take(3) as $cita) {
            $this->actingAs(User::where('role', User::ROLE_ADMIN)->first())
                ->put(route('admin.citas.update', $cita), $this->updatePayload($cita, ['estado' => 'aceptada']))
                ->assertRedirect(route('admin.citas.index'));
        }

        $ultima = $citas->last();
        $this->actingAs(User::where('role', User::ROLE_ADMIN)->first())
            ->from(route('admin.citas.edit', $ultima))
            ->put(route('admin.citas.update', $ultima), $this->updatePayload($ultima, ['estado' => 'aceptada']))
            ->assertSessionHasErrors('estado');

        $this->assertSame('solicitada', $ultima->fresh()->estado);
    }

    public function test_customer_cannot_book_same_day_twice_with_the_same_motorcycle(): void
    {
        [$user, $cliente, $moto] = $this->customerWithMoto();
        $data = $this->appointmentPayload($cliente, $moto, '2026-11-17');

        $this->actingAs($user)
            ->post(route('mis-citas.store'), $data)
            ->assertRedirect(route('mis-citas.index'));

        $this->from(route('mis-citas.create'))
            ->post(route('mis-citas.store'), $data)
            ->assertSessionHasErrors('moto_id');

        $this->assertDatabaseCount('citas', 1);
    }

    public function test_customer_must_wait_until_next_business_day_but_assistant_can_book_today(): void
    {
        [$customer, $cliente, $moto] = $this->customerWithMoto();

        foreach (['2026-11-13', '2026-11-14'] as $fecha) {
            $this->actingAs($customer)
                ->from(route('mis-citas.create'))
                ->post(route('mis-citas.store'), $this->appointmentPayload($cliente, $moto, $fecha))
                ->assertSessionHasErrors('fecha');
        }

        $assistant = User::factory()->asistente()->create();
        $staffCliente = Cliente::factory()->create();
        $staffMoto = Moto::factory()->create(['cliente_id' => $staffCliente->id]);

        $this->actingAs($assistant)
            ->post(route('admin.citas.store'), $this->appointmentPayload($staffCliente, $staffMoto, '2026-11-13'))
            ->assertRedirect(route('admin.citas.index'));

        $this->assertDatabaseHas('citas', [
            'cliente_id' => $staffCliente->id,
            'fecha' => '2026-11-13 00:00:00',
            'hora' => '08:30',
            'estado' => 'solicitada',
        ]);
    }

    public function test_server_uses_catalog_ingress_time_and_saves_observations(): void
    {
        [$user, $cliente, $moto] = $this->customerWithMoto();

        $this->actingAs($user)
            ->post(route('mis-citas.store'), array_merge(
                $this->appointmentPayload($cliente, $moto, '2026-11-17'),
                ['hora' => '17:30', 'observaciones' => 'Vibra al frenar']
            ))
            ->assertRedirect(route('mis-citas.index'));

        $this->assertDatabaseHas('citas', [
            'moto_id' => $moto->id,
            'fecha' => '2026-11-17 00:00:00',
            'hora' => '08:30',
            'servicio_id' => $this->mantenimiento()->id,
            'servicio' => 'Mantenimiento general',
            'observaciones' => 'Vibra al frenar',
            'estado' => 'solicitada',
        ]);
    }

    public function test_non_agendable_service_is_rejected(): void
    {
        [$user, $cliente, $moto] = $this->customerWithMoto();
        $servicio = Servicio::where('nombre', 'Mecánica rápida')->firstOrFail();

        $this->actingAs($user)
            ->from(route('mis-citas.create'))
            ->post(route('mis-citas.store'), array_merge(
                $this->appointmentPayload($cliente, $moto, '2026-11-17'),
                ['servicio_id' => $servicio->id]
            ))
            ->assertSessionHasErrors('servicio_id');

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_create_form_lists_server_calculated_days_and_disables_saturday_booking(): void
    {
        [$user] = $this->customerWithMoto();

        $this->actingAs($user)
            ->get(route('mis-citas.create'))
            ->assertOk()
            ->assertSee('martes 17 de noviembre (3 cupos)')
            ->assertSee('name="fecha"', false)
            ->assertDontSee('name="hora"', false)
            ->assertSee('name="observaciones"', false);
    }

    public function test_form_disables_submission_when_no_service_days_are_available(): void
    {
        config(['taller.dias_max_anticipacion' => 0]);
        [$user] = $this->customerWithMoto();

        $this->actingAs($user)
            ->get(route('mis-citas.create'))
            ->assertOk()
            ->assertSee('No hay días disponibles para este servicio en el rango permitido.')
            ->assertSee('id="submit-cita"', false)
            ->assertSee('disabled', false);
    }

    public function test_changing_the_service_applies_schedule_rules_and_sets_its_catalog_time(): void
    {
        $admin = User::factory()->admin()->create();
        $cita = $this->createCita(null, [
            'fecha' => '2026-11-18',
            'hora' => '13:00:00',
            'servicio' => 'Servicio histórico',
        ]);
        $payload = $this->updatePayload($cita, [
            'fecha' => '2026-11-14',
            'servicio_id' => $this->mantenimiento()->id,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.citas.edit', $cita))
            ->put(route('admin.citas.update', $cita), $payload)
            ->assertSessionHasErrors('fecha');

        $this->assertNull($cita->fresh()->servicio_id);
        $this->assertSame('2026-11-18', $cita->fresh()->fecha->format('Y-m-d'));

        $payload['fecha'] = '2026-11-18';
        $this->put(route('admin.citas.update', $cita), $payload)
            ->assertRedirect(route('admin.citas.index'));

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'servicio_id' => $this->mantenimiento()->id,
            'servicio' => 'Mantenimiento general',
            'hora' => '08:30',
        ]);
    }

    public function test_services_seeder_is_idempotent_and_links_matching_legacy_citations(): void
    {
        $legacy = $this->createCita(null, ['servicio' => 'mAnTeNiMiEnTo GeNeRaL']);
        $other = $this->createCita(null, ['servicio' => 'Cambio de aceite']);
        $mantenimiento = $this->mantenimiento();
        $mantenimiento->update(['dias_anticipacion_minima' => 9]);

        $this->seed(ServiciosSeeder::class);
        $this->seed(ServiciosSeeder::class);

        $this->assertDatabaseCount('servicios', 2);
        $this->assertSame($mantenimiento->id, $legacy->fresh()->servicio_id);
        $this->assertNull($other->fresh()->servicio_id);
        $this->assertSame(1, $this->mantenimiento()->dias_anticipacion_minima);
    }

    public function test_legacy_citations_keep_their_text_and_may_have_a_null_service_relation(): void
    {
        $cita = $this->createCita(null, ['servicio' => 'Servicio histórico']);

        $this->assertSame('Servicio histórico', $cita->fresh()->servicio);
        $this->assertNull($cita->fresh()->servicio_id);
    }

    private function customerWithMoto(): array
    {
        $user = User::factory()->cliente()->create();
        $cliente = Cliente::factory()->create(['user_id' => $user->id]);
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        return [$user, $cliente, $moto];
    }

    private function mantenimiento(): Servicio
    {
        return Servicio::where('nombre', 'Mantenimiento general')->firstOrFail();
    }

    private function appointmentPayload(Cliente $cliente, Moto $moto, string $fecha): array
    {
        return [
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'fecha' => $fecha,
            'hora' => '17:30',
            'servicio_id' => $this->mantenimiento()->id,
        ];
    }

    private function createCita(?Servicio $servicio, array $attributes = []): Cita
    {
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        return Cita::factory()->create(array_merge([
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => null,
            'fecha' => '2026-11-17',
            'hora' => '08:30:00',
            'estado' => 'solicitada',
            'servicio_id' => $servicio?->id,
            'servicio' => $servicio?->nombre ?? 'Mantenimiento general',
        ], $attributes));
    }

    private function updatePayload(Cita $cita, array $overrides = []): array
    {
        return array_merge([
            'cliente_id' => $cita->cliente_id,
            'moto_id' => $cita->moto_id,
            'mecanico_id' => $cita->mecanico_id,
            'fecha' => $cita->fecha->format('Y-m-d'),
            'hora' => substr($cita->hora, 0, 5),
            'estado' => $cita->estado,
            'servicio_id' => $cita->servicio_id,
            'servicio' => $cita->servicio,
            'observaciones' => $cita->observaciones,
        ], $overrides);
    }
}
