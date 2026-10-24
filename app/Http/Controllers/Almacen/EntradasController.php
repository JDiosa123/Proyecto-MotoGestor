<?php

namespace App\Http\Controllers\Almacen;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntradasController extends Controller
{
    public function index()
    {
        $entradas = MovimientoInventario::with('producto')
            ->where('tipo', 'entrada')
            ->orderByDesc('fecha')
            ->get();

        return view('almacen.entradas.index', compact('entradas'));
    }

    public function create()
    {
        return view('almacen.entradas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data): void {
            $producto = Producto::where('nombre', $data['nombre'])
                ->lockForUpdate()
                ->first();

            if (!$producto) {
                $producto = Producto::create([
                    'nombre' => $data['nombre'],
                    'categoria' => $data['categoria'],
                    'precio' => $data['precio'],
                    'cantidad' => 0,
                    'descripcion' => $data['descripcion'] ?? null,
                    'fecha_registro' => now()->toDateString(),
                    'user_id' => Auth::id(),
                ]);
            } else {
                $producto->categoria = $data['categoria'];
                $producto->precio = $data['precio'];
                if (isset($data['descripcion'])) {
                    $producto->descripcion = $data['descripcion'];
                }
            }

            $producto->cantidad += $data['cantidad'];
            $producto->save();

            MovimientoInventario::create([
                'id_producto' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $data['cantidad'],
                'descripcion' => $data['descripcion'] ?? null,
                'fecha' => now()->toDateString(),
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('entradas.index')->with('success', 'Entrada registrada correctamente.');
    }
}
