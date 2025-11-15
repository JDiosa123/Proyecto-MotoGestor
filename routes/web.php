<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MotoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\EntradasController;

Route::get('/', function () {
    return redirect()->route('login');
});

/* ============================
        AUTENTICACIÓN
============================ */
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ============================
        DASHBOARD PRINCIPAL
============================ */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/* ============================
        RUTAS PROTEGIDAS
============================ */
Route::middleware('auth')->group(function () {

    /* --- Gestión de usuarios --- */
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/admin/users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

    /* --- Gestión de clientes --- */
    Route::get('/admin/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index');
    Route::get('/admin/clientes/create', [ClienteController::class, 'create'])->name('admin.clientes.create');
    Route::post('/admin/clientes', [ClienteController::class, 'store'])->name('admin.clientes.store');
    Route::get('/admin/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('admin.clientes.edit');
    Route::put('/admin/clientes/{cliente}', [ClienteController::class, 'update'])->name('admin.clientes.update');
    Route::delete('/admin/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('admin.clientes.destroy');

    // --- Gestión de motos ---
    /* --- Gestión de motos --- */
    Route::get('admin/motos', [MotoController::class, 'index'])->name('admin.motos.index');
    Route::get('admin/motos/create/{cliente?}', [MotoController::class, 'create'])->name('admin.motos.create');
    Route::post('admin/motos/{cliente?}', [MotoController::class, 'store'])->name('admin.motos.store');
    Route::get('admin/motos/{moto}/edit', [MotoController::class, 'edit'])->name('admin.motos.edit');
    Route::put('admin/motos/{moto}', [MotoController::class, 'update'])->name('admin.motos.update');
    Route::delete('admin/motos/{moto}', [MotoController::class, 'destroy'])->name('admin.motos.destroy');

// --- Gestión de citas ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('/citas/create', [CitaController::class, 'create'])->name('citas.create');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/citas/{id_cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');
    Route::put('/citas/{id_cita}', [CitaController::class, 'update'])->name('citas.update');
    Route::delete('/citas/{id_cita}', [CitaController::class, 'destroy'])->name('citas.destroy');
});



    // --- Gestión de inventario ---
    /* --- Gestión de inventario --- */
    Route::get('/almacen', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/almacen/registrar', [InventarioController::class, 'registrarMovimiento'])->name('inventario.registrar');

    /* --- Gestión de productos --- */
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/{id}/editar', [ProductoController::class, 'editar'])->name('productos.editar');
    Route::put('/productos/{id}', [ProductoController::class, 'actualizar'])->name('productos.actualizar');
    Route::delete('/productos/{id}', [ProductoController::class, 'eliminar'])->name('productos.eliminar');
});

/* ============================
        API
============================ */
Route::get('/api/clientes/{cliente}/motos', function (App\Models\Cliente $cliente) {
    return $cliente->motos()->get();
});

/* ============================
        INVENTARIO GENERAL
============================ */
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');

/* --- Salidas --- */
Route::get('/inventario/salidas', [InventarioController::class, 'salidas'])->name('inventario.salidas');
Route::post('/inventario/salidas/guardar', [InventarioController::class, 'guardarSalida'])->name('inventario.salidas.guardar');

/* ============================
        ENTRADAS (CORREGIDO)
============================ */
Route::prefix('almacen/entradas')->group(function () {
    Route::get('/', [EntradasController::class, 'index'])->name('entradas.index');
    Route::get('/crear', [EntradasController::class, 'create'])->name('entradas.create');
    Route::post('/guardar', [EntradasController::class, 'store'])->name('entradas.store');
});

/* ============================
        AUTH EXTRA
============================ */
require __DIR__.'/auth.php';
use App\Http\Controllers\SalidasController;

Route::prefix('inventario/salidas')->group(function () {

    Route::get('/', [SalidasController::class, 'index'])->name('salidas.index');   // LISTADO
    Route::get('/crear', [SalidasController::class, 'create'])->name('salidas.create'); // FORMULARIO
    Route::post('/guardar', [SalidasController::class, 'store'])->name('salidas.store'); // GUARDAR

});