<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;

class EntradasController extends Controller
{
    public function index()
    {
        $entradas = Entrada::with('producto')->latest()->get();
        return view('almacen.entradas.index', compact('entradas'));
    }

    public function create()
    {
        return view('almacen.entradas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|in:Insumos,General,Dotacion',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255'
        ], [
            'categoria.in' => 'Debe seleccionar una categoría válida.'
        ]);

        
        $producto = Producto::where('nombre', $request->nombre)->first();

        
        if (!$producto) {
            $producto = Producto::create([
                'nombre' => $request->nombre,
                'categoria' => $request->categoria,
                'precio' => $request->precio,
                'cantidad' => 0,
                'descripcion' => $request->descripcion,
                'fecha_registro' => now(),
                'user_id' => Auth::id()
        ]);
    } else {
        if ($request->descripcion) {
            $producto->descripcion = $request->descripcion;
        }

        $producto->categoria = $request->categoria;
        $producto->precio = $request->precio;

    }
      
        $producto->cantidad += $request->cantidad;
        $producto->save();

        
        $entrada = Entrada::create([
            'id_producto' => $producto->id_producto,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
            'creado_por' => Auth::user()->name,
            'fecha' => now()
        ]);

    
        
    MovimientoInventario::create([
        'id_producto' => $producto->id_producto,
        'tipo' => 'entrada',
        'cantidad' => $request->cantidad,
        'descripcion' => $request->descripcion,
        'fecha' => now(),
        'user_id' => auth()->id(), 
    ]);



        return redirect()->route('entradas.index')->with('success', 'Entrada registrada correctamente.');
    }
}
