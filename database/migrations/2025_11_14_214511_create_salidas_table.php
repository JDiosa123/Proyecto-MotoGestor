<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Salida;
use Illuminate\Support\Facades\Auth;

class SalidasController extends Controller
{
    /**
     * Mostrar listado de salidas
     */
    public function index()
    {
        
        $salidas = Salida::with('producto')->orderBy('fecha', 'desc')->get();

        return view('almacen.salidas.index', compact('salidas'));
    }

    /**
     * Mostrar formulario para crear una salida
     */
    public function create()
    {
        // Traer productos
        $productos = Producto::orderBy('nombre')->get();

        return view('almacen.salidas.create', compact('productos'));
    }

    /**
     * Guardar nueva salida
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        
        $producto = Producto::findOrFail($request->id_producto);

        
        if ($producto->cantidad < $request->cantidad) {
            return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible.']);
        }

        
        $producto->cantidad -= $request->cantidad;
        $producto->save();

        
        Salida::create([
            'id_producto' => $producto->id_producto,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
            'creado_por' => Auth::user()->name,
            'fecha' => now(),
        ]);

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida registrada correctamente.');
    }

    /**
     * Mostrar una salida (opcional)
     */
    public function show($id)
    {
        $salida = Salida::with('producto')->findOrFail($id);
        return view('almacen.salidas.show', compact('salida'));
    }

    /**
     * Editar una salida (opcional)
     */
    public function edit($id)
    {
        $salida = Salida::findOrFail($id);
        $productos = Producto::orderBy('nombre')->get();

        return view('almacen.salidas.edit', compact('salida', 'productos'));
    }

    /**
     * Actualizar una salida (opcional)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        $salida = Salida::findOrFail($id);

        
        $producto = Producto::findOrFail($request->id_producto);
        $diferencia = $request->cantidad - $salida->cantidad;

        if ($diferencia > 0 && $producto->cantidad < $diferencia) {
            return back()->withErrors(['cantidad' => 'No hay suficiente stock disponible para aumentar la cantidad.']);
        }

        $producto->cantidad -= $diferencia;
        $producto->save();

        $salida->update([
            'id_producto' => $request->id_producto,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('salidas.index')
                    ->with('success', 'Salida actualizada correctamente.');
    }

    /**
     * Eliminar una salida (opcional)
     */
    public function destroy($id)
    {
        $salida = Salida::findOrFail($id);

        
        $producto = Producto::findOrFail($salida->id_producto);
        $producto->cantidad += $salida->cantidad;
        $producto->save();

        $salida->delete();

        return redirect()->route('salidas.index')
                         ->with('success', 'Salida eliminada correctamente.');
    }
}
