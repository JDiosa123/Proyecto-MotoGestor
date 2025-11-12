<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('almacen.productos.index', compact('productos'));
    }

    public function editar($id)
    {
        $producto = Producto::findOrFail($id);
        return view('almacen.productos.edit', compact('producto'));
    }

    public function actualizar(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'precio' => 'required|integer|min:0',
            'cantidad' => 'required|integer|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'precio' => $request->precio,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
