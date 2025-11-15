<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Moto;
use App\Models\Cliente;

class MotoController extends Controller
{
    public function index(Cliente $cliente = null)
    {
        if ($cliente) {
            $motos = Moto::where('cliente_id', $cliente->id_cliente)->get();
        } else {
            $motos = Moto::with('cliente')->get();
        }

        return view('admin.motos.index', compact('motos', 'cliente'));
    }

    public function create(Cliente $cliente = null)
    {

        $clientes = $cliente ? collect([$cliente]) : Cliente::all();
        return view('admin.motos.create', compact('clientes', 'cliente'));
    }

    public function store(Request $request, Cliente $cliente = null)
    {
        $request->validate([
            'cliente_id' => 'required|exists:cliente,id_cliente',
            'placa' => ['required', 'unique:motos,placa', 'regex:/^([A-Z]{3}\d{2}[A-Z]|[A-Z]{2}\d{3})$/'],
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'cilindraje' => 'nullable|string',
            'color' => 'nullable|string',
        ], [
            'cliente_id.exists' => 'Cliente no válido.',
            'placa.unique' => 'La placa ya está registrada.',
            'placa.required' => 'La placa es obligatoria.',
            'placa.regex' => 'Formato inválido. Las placas deben ser XXX00A o XX000.',
            'cilindraje.numeric' => 'El cilindraje debe ser un número.',
        ]);

        Moto::create([
            'cliente_id' => $request->cliente_id,
            'placa' => $request->placa,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'cilindraje' => $request->cilindraje,
            'color' => $request->color,
        ]);

        return redirect()->route('admin.clientes.index')->with('success', 'Moto registrada correctamente.');
    }

    public function edit(Moto $moto)
    {
        $clientes = Cliente::all();
        return view('admin.motos.edit', compact('moto', 'clientes'));
    }

    public function update(Request $request, Moto $moto)
    {
        $request->validate([
            'cliente_id' => 'required|exists:cliente,id_cliente',
            'placa' => ['required', 'regex:/^([A-Z]{3}\d{2}[A-Z]|[A-Z]{2}\d{3})$/', 'unique:motos,placa,' . $moto->id_moto . ',id_moto'],
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'cilindraje' => 'nullable|string',
            'color' => 'nullable|string',
        ],[
            'cliente_id.exists' => 'Cliente no válido.',
            'placa.unique' => 'La placa ya está registrada.',
            'placa.required' => 'La placa es obligatoria.',
            'placa.regex' => 'Formato inválido. Las placas deben ser XXX00A o XX000.',
            'cilindraje.numeric' => 'El cilindraje debe ser un número.',
        ]);

        $moto->update($request->only(['cliente_id','placa','marca','modelo','cilindraje','color']));

        return redirect()->route('admin.motos.index')->with('success', 'Moto actualizada correctamente.');
    }

    public function destroy(Moto $moto)
    {
        $moto->delete();
        return back()->with('success', 'Moto eliminada correctamente.');
    }
}