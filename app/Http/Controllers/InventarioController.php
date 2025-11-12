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
        // Nota: Si usas esta variable en la vista, asegúrate de tener una tabla de 'productos'.
        $productos = Producto::orderBy('nombre')->get(); 
        
        // Carga los movimientos y la información del producto asociado.
        $movimientos = MovimientoInventario::with('producto')->orderBy('fecha', 'desc')->get();
        
        return view('almacen.index', compact('productos', 'movimientos'));
    }

    public function registrarMovimiento(Request $request)
    {
        $request->validate([
            'producto_nombre' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // Encuentra o crea el producto
        $producto = Producto::firstOrCreate(
            ['nombre' => $request->producto_nombre],
            [
                'categoria' => $request->categoria,
                'precio' => $request->precio ?? 0,
                'cantidad' => 0,
                'fecha_registro' => now(),
                'user_id' => Auth::id(),
            ]
        );

        // Actualizar precio/categoría si se proporcionan nuevos valores
        if ($request->filled('precio') && $producto->precio != $request->precio) {
            $producto->precio = $request->precio;
        }
        if ($request->filled('categoria') && $producto->categoria != $request->categoria) {
            $producto->categoria = $request->categoria;
        }

        // Modificar stock
        if ($request->tipo === 'entrada') {
            $producto->cantidad += $request->cantidad;
        } else {
            if ($producto->cantidad < $request->cantidad) {
                return back()->with('error', 'No hay suficiente stock disponible.');
            }
            $producto->cantidad -= $request->cantidad;
        }

        $producto->save();

        // Crear registro de movimiento
        MovimientoInventario::create([
            'id_producto' => $producto->id_producto,
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
            'user_id' => Auth::id(),
            'fecha' => now(),
        ]);

        // ✅ REDIRECCIÓN CORREGIDA
        return redirect()->route('inventario.index')->with('success', 'Movimiento registrado correctamente.');
    }

    public function editar($id)
    {
        $movimiento = MovimientoInventario::with('producto')->findOrFail($id);
        // La variable $productos se eliminó de aquí previamente, si no la usas.
        return view('almacen.editMovimiento', compact('movimiento'));
    }

    public function actualizar(Request $request, $id)
    {
        $movimiento = MovimientoInventario::findOrFail($id);
        $producto = Producto::findOrFail($movimiento->id_producto);

        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255',
        ]);

        // 1. Revertir movimiento anterior
        if ($movimiento->tipo === 'entrada') {
            $producto->cantidad -= $movimiento->cantidad;
        } else {
            $producto->cantidad += $movimiento->cantidad;
        }

        // 2. Aplicar nuevo movimiento
        if ($request->tipo === 'entrada') {
            $producto->cantidad += $request->cantidad;
        } else {
            // Verificar stock después de la reversión
            if ($producto->cantidad < $request->cantidad) {
                // Revertir la reversión si falla la verificación de stock para mantener la integridad
                if ($movimiento->tipo === 'entrada') {
                    $producto->cantidad += $movimiento->cantidad;
                } else {
                    $producto->cantidad -= $movimiento->cantidad;
                }
                $producto->save();
                return back()->with('error', 'No hay suficiente stock para la salida solicitada.');
            }
            $producto->cantidad -= $request->cantidad;
        }

        $producto->save();

        $movimiento->update([
            'tipo' => $request->tipo,
            'cantidad' => $request->cantidad,
            'descripcion' => $request->descripcion,
        ]);

        // ✅ REDIRECCIÓN CORREGIDA
        return redirect()->route('inventario.index')->with('success', 'Movimiento actualizado correctamente.');
    }

    public function eliminar($id)
    {
        $movimiento = MovimientoInventario::findOrFail($id);
        $producto = Producto::findOrFail($movimiento->id_producto);

        // Revertir el stock antes de eliminar el movimiento
        if ($movimiento->tipo === 'entrada') {
            $producto->cantidad -= $movimiento->cantidad;
        } else {
            $producto->cantidad += $movimiento->cantidad;
        }

        $producto->save();
        $movimiento->delete();

        // Opcional: Eliminar producto si el stock llega a 0 o menos
        if ($producto->cantidad <= 0) {
             $producto->delete(); 
        }

        // ✅ REDIRECCIÓN CORREGIDA
        return redirect()->route('inventario.index')->with('success', 'Movimiento eliminado correctamente.');
    }
}