<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelCitaRequest;
use App\Http\Requests\RejectCitaRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\Servicio;
use App\Models\User;
use App\Services\HorarioTaller;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $citas = Cita::visibleTo($request->user())
            ->with(['cliente', 'moto', 'mecanico', 'servicioCatalogo'])
            ->orderByDesc('fecha')
            ->get();

        return view('admin.citas.index', compact('citas'));
    }

    public function customerCreate(Request $request)
    {
        $cliente = $request->user()->cliente;
        abort_unless($cliente, 403, 'La cuenta no tiene una ficha de cliente vinculada.');

        return view('admin.citas.create', array_merge([
            'clientes' => collect([$cliente]),
            'mecanicos' => collect(),
            'motos' => $cliente->motos,
            'esCliente' => true,
        ], $this->appointmentFormData($request->user())));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::all();
        $mecanicos = User::where('role', User::ROLE_MECHANIC)->get();

        return view('admin.citas.create', array_merge(
            compact('clientes', 'mecanicos'),
            $this->appointmentFormData($request->user())
        ));
    }

    public function store(StoreCitaRequest $request)
    {
        $data = $request->validated();

        if ($this->hasScheduleConflict($data)) {
            return back()
                ->with('error', 'El mecánico o el cliente ya tiene una cita a esa hora.')
                ->withInput();
        }

        $esCliente = $request->user()->role === User::ROLE_CLIENT;

        Cita::create($data + [
            'estado' => 'solicitada',
            'motivo_cancelacion' => null,
        ]);

        return redirect()
            ->route($esCliente ? 'mis-citas.index' : 'admin.citas.index')
            ->with('success', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $clientes = Cliente::all();
        $mecanicos = User::where('role', User::ROLE_MECHANIC)->get();
        $motos = Moto::where('cliente_id', $cita->cliente_id)->get();
        $servicios = Servicio::query()->where('activo', true)->where('agendable', true)->get();
        if ($cita->servicioCatalogo && ! $servicios->contains('id', $cita->servicioCatalogo->id)) {
            $servicios->prepend($cita->servicioCatalogo);
        }

        return view('admin.citas.edit', compact('cita', 'clientes', 'mecanicos', 'motos', 'servicios'));
    }

    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        $data = $request->validated();
        $esAsistente = $request->user()->role === User::ROLE_ASSISTANT;

        if ($esAsistente) {
            $data = [
                'cliente_id' => $cita->cliente_id,
                'moto_id' => $cita->moto_id,
                'mecanico_id' => $data['mecanico_id'] ?? $cita->mecanico_id,
                'fecha' => $cita->fecha->format('Y-m-d'),
                'hora' => $cita->hora,
                'estado' => $data['estado'],
                'servicio' => $cita->servicio,
                'motivo_cancelacion' => $data['motivo_cancelacion'] ?? null,
            ];
        } elseif (isset($data['servicio_id'])) {
            $servicio = Servicio::findOrFail($data['servicio_id']);
            $fechaCambia = CarbonImmutable::parse($data['fecha'], config('app.timezone'))->toDateString()
                !== $cita->fecha->format('Y-m-d');
            $horaCambia = substr($data['hora'], 0, 5) !== substr($cita->hora, 0, 5);
            $servicioCambia = (int) $data['servicio_id'] !== (int) $cita->servicio_id;

            if ($fechaCambia || $horaCambia || $servicioCambia) {
                if ($servicio->hora_ingreso) {
                    $data['hora'] = substr($servicio->hora_ingreso, 0, 5);
                }
            }

            if ($servicioCambia) {
                $data['servicio'] = $servicio->nombre;
            } else {
                unset($data['servicio']);
            }
        }

        if ($this->hasScheduleConflict($data, $cita)) {
            return back()
                ->with('error', 'El mecánico o el cliente ya tiene una cita a esa hora.')
                ->withInput();
        }

        if ($data['estado'] !== 'cancelada') {
            $data['motivo_cancelacion'] = null;
        }

        $updates = $esAsistente
            ? array_intersect_key($data, array_flip(['mecanico_id', 'estado', 'motivo_cancelacion']))
            : $data;

        $this->updateWithCapacityCheck($cita, $updates);

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada.');
    }

    public function reject(RejectCitaRequest $request, Cita $cita)
    {
        $updated = Cita::whereKey($cita->getKey())
            ->where('estado', 'solicitada')
            ->update([
                'estado' => 'cancelada',
                'motivo_cancelacion' => $request->validated('motivo_cancelacion'),
            ]);

        abort_unless($updated === 1, 422, 'Solo se pueden rechazar citas solicitadas.');

        return back()->with('success', 'Cita rechazada correctamente.');
    }

    public function attend(Request $request, Cita $cita)
    {
        $cita = Cita::visibleTo($request->user())->findOrFail($cita->id);
        abort_if($cita->estado === 'cancelada', 403);
        $this->updateWithCapacityCheck($cita, ['estado' => 'atendida']);

        return back()->with('success', 'Cita marcada como atendida.');
    }

    public function cancelByClient(CancelCitaRequest $request, Cita $cita)
    {
        $cita = Cita::visibleTo($request->user())->findOrFail($cita->id);
        abort_if(in_array($cita->estado, ['cancelada', 'atendida'], true), 403);

        $cita->update([
            'estado' => 'cancelada',
            'motivo_cancelacion' => $request->validated('motivo_cancelacion'),
        ]);

        return back()->with('success', 'Cita cancelada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return back()->with('success', 'Cita eliminada.');
    }

    private function hasScheduleConflict(array $data, ?Cita $cita = null): bool
    {
        if (($data['estado'] ?? null) === 'cancelada') {
            return false;
        }

        $conflicts = static function (Builder $query) use ($data, $cita): void {
            $query->where('fecha', $data['fecha'])
                ->where('hora', $data['hora'])
                ->where('estado', '!=', 'cancelada');

            if ($cita) {
                $query->whereKeyNot($cita->getKey());
            }
        };

        $clientConflict = Cita::where('cliente_id', $data['cliente_id'])
            ->where($conflicts)
            ->exists();

        if ($clientConflict) {
            return true;
        }

        return ! empty($data['mecanico_id'])
            && Cita::where('mecanico_id', $data['mecanico_id'])
                ->where($conflicts)
                ->exists();
    }

    private function appointmentFormData(User $user): array
    {
        $horario = app(HorarioTaller::class);
        $servicios = Servicio::query()
            ->where('activo', true)
            ->where('agendable', true)
            ->orderBy('nombre')
            ->get();
        $diasDisponiblesPorServicio = [];

        foreach ($servicios as $servicio) {
            $desde = now(config('app.timezone'))->toDateString();
            if ($user->role === User::ROLE_CLIENT) {
                for ($dias = 0; $dias < $servicio->dias_anticipacion_minima; $dias++) {
                    $desde = $horario->siguienteDiaHabil($desde);
                }
            }

            $diasDisponiblesPorServicio[$servicio->id] = $horario
                ->diasDisponibles($servicio, $desde)
                ->map(function (array $dia): array {
                    $dia['etiqueta'] = CarbonImmutable::parse($dia['fecha'], config('app.timezone'))
                        ->locale('es')
                        ->translatedFormat('l d \d\e F');

                    return $dia;
                })
                ->values()
                ->all();
        }

        return compact('servicios', 'diasDisponiblesPorServicio');
    }

    private function updateWithCapacityCheck(Cita $cita, array $updates): void
    {
        $nuevoEstado = $updates['estado'] ?? $cita->estado;
        $fecha = isset($updates['fecha'])
                ? CarbonImmutable::parse($updates['fecha'], config('app.timezone'))->toDateString()
                : $cita->fecha->format('Y-m-d');
        $servicioId = array_key_exists('servicio_id', $updates)
                ? $updates['servicio_id']
                : $cita->servicio_id;
        $servicio = $servicioId ? Servicio::find($servicioId) : null;
        $fechaOCatalogoCambia = $fecha !== $cita->fecha->format('Y-m-d')
                || ($servicioId === null ? null : (int) $servicioId)
                    !== ($cita->servicio_id === null ? null : (int) $cita->servicio_id);
        $entraEnCupo = in_array($nuevoEstado, ['aceptada', 'atendida'], true)
                && $servicio?->ocupa_dia_completo
                && (! in_array($cita->estado, ['aceptada', 'atendida'], true) || $fechaOCatalogoCambia);

        DB::transaction(function () use ($cita, $updates, $entraEnCupo, $fecha): void {
            if ($entraEnCupo) {
                $siguienteDia = CarbonImmutable::parse($fecha, config('app.timezone'))
                    ->addDay()
                    ->toDateString();
                $citasDelDia = Cita::query()
                    ->where('fecha', '>=', $fecha)
                    ->where('fecha', '<', $siguienteDia)
                    ->with('servicioCatalogo')
                    ->lockForUpdate()
                    ->get();

                $ocupados = $citasDelDia->filter(fn (Cita $dia) => $dia->getKey() !== $cita->getKey()
                    && in_array($dia->estado, ['aceptada', 'atendida'], true)
                    && $dia->servicioCatalogo?->ocupa_dia_completo)->count();

                if ($ocupados >= app(HorarioTaller::class)->cupoDelDia($fecha)) {
                    throw ValidationException::withMessages([
                        'estado' => 'Ese día ya no tiene cupos disponibles',
                    ]);
                }
            }

            $cita->update($updates);
        });
    }
}
