<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalidasController extends Controller
{
    public function index()
    {
        $salidas = MovimientoInventario::with('producto')
            ->where('tipo', 'salida')
            ->orderByDesc('fecha')
            ->get();

        return view('almacen.salidas.index', compact('salidas'));
    }

    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();

        return view('almacen.salidas.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_producto' => ['required', 'exists:productos,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'fecha' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($data): void {
            $producto = Producto::whereKey($data['id_producto'])->lockForUpdate()->firstOrFail();

            if ($producto->cantidad < $data['cantidad']) {
                throw ValidationException::withMessages([
                    'cantidad' => 'No hay suficiente stock para esta salida.',
                ]);
            }

            $producto->cantidad -= $data['cantidad'];
            $producto->save();

            MovimientoInventario::create([
                'id_producto' => $producto->id,
                'tipo' => 'salida',
                'cantidad' => $data['cantidad'],
                'descripcion' => $data['descripcion'] ?? null,
                'fecha' => $data['fecha'] ?? now()->toDateString(),
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('salidas.index')->with('success', 'Salida registrada correctamente.');
    }
}
