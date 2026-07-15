<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Moto;
use App\Models\User;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    /**
     * Mostrar todas las citas
     */
    public function index()
    {
        $citas = Cita::with(['cliente', 'moto', 'mecanico'])->orderBy('fecha', 'desc')->get();

        return view('admin.citas.index', compact('citas'));
    }

    /**
     * Formulario para crear cita
     */
    public function create()
    {
        $clientes = Cliente::all();
        $mecanicos = User::where('role', 'mecanico')->get(); // si usas rol en tabla users

        return view('admin.citas.create', compact('clientes', 'mecanicos'));
    }

    /**
     * Guardar cita en la BD
     */
    public function store(StoreCitaRequest $request)
    {
        $data = $request->validated();

        // 1️ Validar disponibilidad del mecánico
        $existeMecanico = Cita::where('mecanico_id', $data['mecanico_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->exists();

        if ($existeMecanico) {
            return back()
                ->with('error', 'El mecánico ya tiene una cita a esa hora.')
                ->withInput();
        }

        // 2 Validar que el cliente no tenga cita duplicada
        $existeCliente = Cita::where('cliente_id', $data['cliente_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->exists();

        if ($existeCliente) {
            return back()
                ->with('error', 'El cliente ya tiene una cita a esa hora.')
                ->withInput();
        }

        // 3 Registrar la cita
        Cita::create([
            'cliente_id' => $data['cliente_id'],
            'moto_id' => $data['moto_id'],
            'mecanico_id' => $data['mecanico_id'],
            'fecha' => $data['fecha'],
            'hora' => $data['hora'],
            'estado' => 'Agendada',
        ]);

        return redirect()->route('admin.citas.index')->with('success', 'Cita agendada correctamente.');
    }


    /**
     * Editar cita
     */
    public function edit($id)
    {
        $cita = Cita::findOrFail($id);
        $clientes = Cliente::all();
        $mecanicos = User::where('role', 'mecanico')->get();
        $motos = Moto::where('cliente_id', $cita->cliente_id)->get();

        return view('admin.citas.edit', compact('cita', 'clientes', 'mecanicos', 'motos'));
    }

    /**
     * Actualizar cita
     */
    public function update(UpdateCitaRequest $request, $id)
    {
        $cita = Cita::findOrFail($id);

        $data = $request->validated();

        // Validar disponibilidad mecánico
        $existeMecanico = Cita::where('mecanico_id', $data['mecanico_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->where('id_cita', '!=', $id)
            ->exists();

        if ($existeMecanico) {
            return back()->with('error', 'El mecánico ya tiene una cita a esa hora.')->withInput();
        }

        // Validar disponibilidad cliente
        $existeCliente = Cita::where('cliente_id', $data['cliente_id'])
            ->where('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->where('id_cita', '!=', $id)
            ->exists();

        if ($existeCliente) {
            return back()->with('error', 'El cliente ya tiene una cita a esa hora.')->withInput();
        }

        $cita->update($data);

        return redirect()->route('admin.citas.index')->with('success', 'Cita actualizada.');
    }

    /**
     * Eliminar cita
     */
    public function destroy($id)
    {
        Cita::findOrFail($id)->delete();

        return back()->with('success', 'Cita eliminada.');
    }
}
