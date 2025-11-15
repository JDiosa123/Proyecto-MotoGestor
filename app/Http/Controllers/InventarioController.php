<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{

    public function index()
{
    
    $productos = Producto::orderBy('nombre')->get();

    
    $movimientos = MovimientoInventario::with('producto')
        ->orderBy('fecha', 'desc')
        ->get();

    
    return view('almacen.movimientos.index', compact('productos', 'movimientos'));
}

    public function entradas()
    {
        $productos = Producto::orderBy('nombre')->get();

        $movimientos = MovimientoInventario::with('producto')
            ->where('tipo', 'entrada')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('almacen.entradas.index', compact('productos', 'movimientos'));
    }


    
    public function guardarEntrada(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255|unique:producto,nombre',
            'categoria'  => 'required|string|in:Insumos,Repuestos,Otros',
            'precio'     => 'required|integer|min:0',
            'cantidad'   => 'required|integer|min:1',
            'descripcion'=> 'nullable|string|max:255',
        ]);

        
        $producto = Producto::create([
            'nombre'         => $request->nombre,
            'categoria'      => $request->categoria,
            'precio'         => $request->precio,
            'cantidad'       => $request->cantidad,
            'fecha_registro' => now(),
            'user_id'        => Auth::id(),
        ]);

        
        MovimientoInventario::create([
            'id_producto' => $producto->id_producto,
            'tipo'        => 'entrada',
            'cantidad'    => $request->cantidad,
            'descripcion' => $request->descripcion,
            'user_id'     => Auth::id(),
            'fecha'       => now(),
        ]);

        return back()->with('success', 'Entrada registrada correctamente.');
    }



    
    public function salidas()
    {
        $productos = Producto::orderBy('nombre')->get();

        $movimientos = MovimientoInventario::with('producto')
            ->where('tipo', 'salida')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('almacen.salidas.index', compact('productos', 'movimientos'));
    }


    
    public function guardarSalida(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad'    => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $producto = Producto::find($request->id_producto);

        if ($producto->cantidad < $request->cantidad) {
            return back()->with('error', 'No hay suficiente stock para esta salida.');
        }

        
        $producto->cantidad -= $request->cantidad;
        $producto->save();

        
        MovimientoInventario::create([
            'id_producto' => $producto->id_producto,
            'tipo'        => 'salida',
            'cantidad'    => $request->cantidad,
            'descripcion' => $request->descripcion,
            'user_id'     => Auth::id(),
            'fecha'       => now(),
        ]);

        return back()->with('success', 'Salida registrada correctamente.');
    }

}
