<?php

namespace App\Http\Controllers\Motos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMotoRequest;
use App\Http\Requests\UpdateMotoRequest;
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

    public function store(StoreMotoRequest $request, Cliente $cliente = null)
    {
        Moto::create($request->validated());

        return redirect()->route('admin.clientes.index')->with('success', 'Moto registrada correctamente.');
    }

    public function edit(Moto $moto)
    {
        $clientes = Cliente::all();
        return view('admin.motos.edit', compact('moto', 'clientes'));
    }

    public function update(UpdateMotoRequest $request, Moto $moto)
    {
        $moto->update($request->validated());

        return redirect()->route('admin.motos.index')->with('success', 'Moto actualizada correctamente.');
    }

    public function destroy(Moto $moto)
    {
        $moto->delete();
        return back()->with('success', 'Moto eliminada correctamente.');
    }
}