<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelCitaRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $citas = Cita::visibleTo($request->user())
            ->with(['cliente', 'moto', 'mecanico'])
            ->orderByDesc('fecha')
            ->get();

        return view('admin.citas.index', compact('citas'));
    }

    public function customerCreate(Request $request)
    {
        $cliente = $request->user()->cliente;
        abort_unless($cliente, 403, 'La cuenta no tiene una ficha de cliente vinculada.');

        return view('admin.citas.create', [
            'clientes' => collect([$cliente]),
            'mecanicos' => collect(),
            'esCliente' => true,
        ]);
    }

    public function create()
    {
        $clientes = Cliente::all();
        $mecanicos = User::where('role', User::ROLE_MECHANIC)->get();

        return view('admin.citas.create', compact('clientes', 'mecanicos'));
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

        return view('admin.citas.edit', compact('cita', 'clientes', 'mecanicos', 'motos'));
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
        }

        if ($this->hasScheduleConflict($data, $cita)) {
            return back()
                ->with('error', 'El mecánico o el cliente ya tiene una cita a esa hora.')
                ->withInput();
        }

        if ($data['estado'] !== 'cancelada') {
            $data['motivo_cancelacion'] = null;
        }

        $cita->update($esAsistente
            ? array_intersect_key($data, array_flip(['mecanico_id', 'estado', 'motivo_cancelacion']))
            : $data);

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada.');
    }

    public function attend(Request $request, Cita $cita)
    {
        $cita = Cita::visibleTo($request->user())->findOrFail($cita->id);
        abort_if($cita->estado === 'cancelada', 403);
        $cita->update(['estado' => 'atendida']);

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

        return !empty($data['mecanico_id'])
            && Cita::where('mecanico_id', $data['mecanico_id'])
                ->where($conflicts)
                ->exists();
    }
}
