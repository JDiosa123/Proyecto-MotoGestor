<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// === Autenticacino ===
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// === Dashboard principal ===
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// === Rutas protegidas por la autenticacion ===
Route::middleware('auth')->group(function () {

    // --- Gestión de usuarios ---
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/admin/users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggleStatus');

    // --- Gestión de clientes ---
    Route::get('/admin/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index');
    Route::get('/admin/clientes/create', [ClienteController::class, 'create'])->name('admin.clientes.create');
    Route::post('/admin/clientes', [ClienteController::class, 'store'])->name('admin.clientes.store');
    Route::get('/admin/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('admin.clientes.edit');
    Route::put('/admin/clientes/{cliente}', [ClienteController::class, 'update'])->name('admin.clientes.update');
    Route::delete('/admin/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('admin.clientes.destroy');

    // --- Gestión de inventario ---
    Route::get('/almacen', [InventarioController::class, 'index'])->name('inventario.index');
    Route::post('/almacen/registrar', [InventarioController::class, 'registrarMovimiento'])->name('inventario.registrar');

    // --- Gestión de productos ---
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('/productos/{id}/editar', [ProductoController::class, 'editar'])->name('productos.editar');
    Route::put('/productos/{id}', [ProductoController::class, 'actualizar'])->name('productos.actualizar');
    Route::delete('/productos/{id}', [ProductoController::class, 'eliminar'])->name('productos.eliminar');
});

require __DIR__.'/auth.php';
