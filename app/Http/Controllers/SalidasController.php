<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidasController extends Controller
{
    public function index()
    {
        $salidas = Salida::with('producto')->latest()->get();
        return view('almacen.salidas.index', compact('salidas'));
    }

    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('almacen.salidas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255'
        ]);

        
        $producto = Producto::findOrFail($request->id_producto);

        
        if ($producto->cantidad < $request->cantidad) {
            return back()
                ->withInput()
                ->with('error', 'No hay suficiente stock para esta salida.');
        }

        DB::transaction(function () use ($request, $producto) {

            
            $producto->cantidad -= $request->cantidad;
            $producto->save();

            
            Salida::create([
                'id_producto' => $producto->id_producto,
                'cantidad' => $request->cantidad,
                'descripcion' => $request->descripcion,
                'creado_por' => Auth::user()->name,
                'fecha' => now(),
            ]);

            
            MovimientoInventario::create([
                'id_producto' => $producto->id_producto,
                'tipo' => 'salida',
                'cantidad' => $request->cantidad,
                'descripcion' => $request->descripcion,
                'fecha' => now(),
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()
            ->route('salidas.index')
            ->with('success', 'Salida registrada correctamente.');
    }
}
