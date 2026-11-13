<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\DiaNoLaborable;
use App\Models\Moto;
use App\Models\User;
use App\Services\HorarioTaller;
use Database\Seeders\DiasNoLaborablesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AppointmentScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-11-12 10:15:00', 'America/Bogota'));
        $this->seed(DiasNoLaborablesSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_schedule_rules_accept_and_reject_weekday_times(): void
    {
        foreach ([
            ['2026-11-23', '08:00', false],
            ['2026-11-23', '08:30', true],
            ['2026-11-23', '17:30', true],
            ['2026-11-23', '18:00', false],
            ['2026-11-23', '08:50', false],
        ] as [$fecha, $hora, $accepted]) {
            $response = $this->submitAppointment($fecha, $hora);

            if ($accepted) {
                $response->assertRedirect(route('admin.citas.index'));
            } else {
                $response->assertSessionHasErrors('fecha');
            }
        }
    }

    public function test_schedule_rules_accept_and_reject_saturday_and_sunday(): void
    {
        foreach ([
            ['2026-11-14', '11:30', true],
            ['2026-11-14', '12:00', false],
            ['2026-11-14', '14:00', false],
            ['2026-11-15', '10:00', false],
        ] as [$fecha, $hora, $accepted]) {
            $response = $this->submitAppointment($fecha, $hora);

            if ($accepted) {
                $response->assertRedirect(route('admin.citas.index'));
            } else {
                $response->assertSessionHasErrors('fecha');
            }
        }
    }

    public function test_holidays_and_saturday_holidays_are_rejected(): void
    {
        $this->submitAppointment('2026-12-08', '10:00')
            ->assertSessionHasErrors('fecha');

        DiaNoLaborable::updateOrCreate(
            ['fecha' => '2026-11-14'],
            ['descripcion' => 'Festivo de prueba']
        );

        $this->submitAppointment('2026-11-14', '11:30')
            ->assertSessionHasErrors('fecha');
    }

    public function test_horario_taller_reports_workdays_and_valid_times(): void
    {
        $horario = app(HorarioTaller::class);

        $this->assertTrue($horario->esDiaLaboral('2026-11-23'));
        $this->assertTrue($horario->esDiaLaboral('2026-11-14'));
        $this->assertFalse($horario->esDiaLaboral('2026-11-15'));
        $this->assertFalse($horario->esDiaLaboral('2026-12-08'));

        $this->assertTrue($horario->esHoraValida('2026-11-23', '08:30'));
        $this->assertTrue($horario->esHoraValida('2026-11-14', '11:30'));
        $this->assertFalse($horario->esHoraValida('2026-11-23', '08:50'));
        $this->assertFalse($horario->esHoraValida('2026-11-15', '10:00'));
        $this->assertFalse($horario->esHoraValida('2026-12-08', '10:00'));
    }

    public function test_customer_cannot_book_yesterday_or_a_past_time_today(): void
    {
        [$customer, $cliente, $moto] = $this->customerWithMoto();

        foreach ([
            [now(config('app.timezone'))->subDay()->toDateString(), '11:00'],
            [now(config('app.timezone'))->toDateString(), '09:30'],
        ] as [$fecha, $hora]) {
            $this->actingAs($customer)
                ->from(route('mis-citas.create'))
                ->post(route('mis-citas.store'), $this->appointmentPayload($cliente, $moto, $fecha, $hora))
                ->assertRedirect(route('mis-citas.create'))
                ->assertSessionHasErrors('fecha');
        }

        $this->assertDatabaseCount('citas', 0);
    }

    public function test_customer_can_book_tomorrow_but_admin_and_assistant_can_book_past_times_today(): void
    {
        [$customer, $cliente, $moto] = $this->customerWithMoto();

        $this->actingAs($customer)
            ->post(route('mis-citas.store'), $this->appointmentPayload(
                $cliente,
                $moto,
                now(config('app.timezone'))->addDay()->toDateString(),
                '10:30'
            ))
            ->assertRedirect(route('mis-citas.index'));

        foreach ([
            User::factory()->admin()->create(),
            User::factory()->asistente()->create(),
        ] as $staff) {
            $staffCliente = Cliente::factory()->create();
            $staffMoto = Moto::factory()->create(['cliente_id' => $staffCliente->id]);

            $this->actingAs($staff)
                ->post(route('admin.citas.store'), $this->appointmentPayload(
                    $staffCliente,
                    $staffMoto,
                    now(config('app.timezone'))->toDateString(),
                    '09:30'
                ))
                ->assertRedirect(route('admin.citas.index'));
        }

        $this->assertDatabaseCount('citas', 3);
    }

    public function test_changing_only_appointment_state_does_not_validate_a_past_or_off_hours_schedule(): void
    {
        $admin = User::factory()->admin()->create();
        $cita = $this->createCita([
            'fecha' => '2020-01-05',
            'hora' => '22:10:00',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.citas.update', $cita), $this->updatePayload($cita, ['estado' => 'aceptada']))
            ->assertRedirect(route('admin.citas.index'));

        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'aceptada']);
    }

    public function test_changing_appointment_date_to_sunday_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $cita = $this->createCita([
            'fecha' => '2026-11-23',
            'hora' => '10:00:00',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.citas.edit', $cita))
            ->put(route('admin.citas.update', $cita), $this->updatePayload($cita, [
                'fecha' => '2026-11-15',
            ]))
            ->assertRedirect(route('admin.citas.edit', $cita))
            ->assertSessionHasErrors('fecha');

        $this->assertSame('2026-11-23', $cita->fresh()->fecha->format('Y-m-d'));
    }

    public function test_holiday_seeder_updates_existing_records_without_duplicates(): void
    {
        DiaNoLaborable::whereDate('fecha', '2026-11-16')->update(['descripcion' => 'Descripción anterior']);

        $this->seed(DiasNoLaborablesSeeder::class);
        $this->seed(DiasNoLaborablesSeeder::class);

        $this->assertDatabaseCount('dias_no_laborables', 3);
        $this->assertDatabaseHas('dias_no_laborables', [
            'fecha' => '2026-11-16',
            'descripcion' => 'Independencia de Cartagena',
        ]);
    }

    public function test_create_form_shows_today_and_configured_time_help_but_edit_has_no_minimum_date(): void
    {
        $admin = User::factory()->admin()->create();
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);
        $cita = $this->createCita(['cliente_id' => $cliente->id]);
        $today = now(config('app.timezone'))->toDateString();

        $this->actingAs($admin)
            ->get(route('admin.citas.create'))
            ->assertOk()
            ->assertSee('min="'.$today.'"', false)
            ->assertSee('step="1800"', false)
            ->assertSee('max="17:30"', false)
            ->assertSee('08:30');

        $edit = $this->get(route('admin.citas.edit', $cita))->assertOk();
        $edit->assertDontSee('name="fecha" min=', false);
    }

    private function submitAppointment(string $fecha, string $hora)
    {
        $admin = User::factory()->admin()->create();
        $cliente = Cliente::factory()->create();
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        return $this->actingAs($admin)
            ->from(route('admin.citas.create'))
            ->post(route('admin.citas.store'), $this->appointmentPayload($cliente, $moto, $fecha, $hora));
    }

    private function appointmentPayload(Cliente $cliente, Moto $moto, string $fecha, string $hora): array
    {
        return [
            'cliente_id' => $cliente->id,
            'moto_id' => $moto->id,
            'mecanico_id' => null,
            'fecha' => $fecha,
            'hora' => $hora,
            'servicio' => 'Cambio de aceite',
        ];
    }

    private function customerWithMoto(): array
    {
        $customer = User::factory()->cliente()->create();
        $cliente = Cliente::factory()->create(['user_id' => $customer->id]);
        $moto = Moto::factory()->create(['cliente_id' => $cliente->id]);

        return [$customer, $cliente, $moto];
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
            'estado' => 'solicitada',
        ], $attributes, ['moto_id' => $moto->id]));
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
            'servicio' => $cita->servicio,
            'motivo_cancelacion' => null,
        ], $overrides);
    }
}
