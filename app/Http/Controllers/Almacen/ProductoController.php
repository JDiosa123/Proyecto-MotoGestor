<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProductoRequest;
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

    public function actualizar(UpdateProductoRequest $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $producto->update($request->validated());

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
