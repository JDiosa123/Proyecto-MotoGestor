<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('almacen.productos.index', compact('productos'));
    }

    public function create()
    {
        return view('almacen.productos.create');
    }

    public function store(StoreProductoRequest $request)
    {
        Producto::create($request->validated() + [
            'cantidad' => 0,
            'stock_minimo' => 0,
            'fecha_registro' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
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
