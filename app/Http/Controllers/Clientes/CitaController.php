<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with(['cliente', 'moto', 'mecanico'])->orderByDesc('fecha')->get();

        return view('admin.citas.index', compact('citas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $mecanicos = User::all();

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

        Cita::create($data + [
            'estado' => 'solicitada',
            'motivo_cancelacion' => null,
        ]);

        return redirect()->route('admin.citas.index')->with('success', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $clientes = Cliente::all();
        $mecanicos = User::all();
        $motos = Moto::where('cliente_id', $cita->cliente_id)->get();

        return view('admin.citas.edit', compact('cita', 'clientes', 'mecanicos', 'motos'));
    }

    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        $data = $request->validated();

        if ($this->hasScheduleConflict($data, $cita)) {
            return back()
                ->with('error', 'El mecánico o el cliente ya tiene una cita a esa hora.')
                ->withInput();
        }

        if ($data['estado'] !== 'cancelada') {
            $data['motivo_cancelacion'] = null;
        }

        $cita->update($data);

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada.');
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
