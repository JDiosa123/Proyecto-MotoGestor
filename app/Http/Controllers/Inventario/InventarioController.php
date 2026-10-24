<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('nombre')->get();
        $movimientos = MovimientoInventario::with('producto')
            ->orderByDesc('fecha')
            ->get();

        return view('almacen.movimientos.index', compact('productos', 'movimientos'));
    }
}
