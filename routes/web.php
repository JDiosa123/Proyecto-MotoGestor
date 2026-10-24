<?php

use App\Http\Controllers\Almacen\EntradasController;
use App\Http\Controllers\Almacen\ProductoController;
use App\Http\Controllers\Almacen\SalidasController;
use App\Http\Controllers\Clientes\CitaController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Inventario\InventarioController;
use App\Http\Controllers\Motos\MotoController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

$adminOnly = 'role:'.User::ROLE_ADMIN;
$managementRoles = 'role:'.implode(',', [User::ROLE_ADMIN, User::ROLE_ASSISTANT]);
$inventoryReadRoles = 'role:'.implode(',', [User::ROLE_ADMIN, User::ROLE_STOREKEEPER, User::ROLE_ASSISTANT]);
$inventoryWriteRoles = 'role:'.implode(',', [User::ROLE_ADMIN, User::ROLE_STOREKEEPER]);
$productReadRoles = 'role:'.implode(',', [User::ROLE_ADMIN, User::ROLE_STOREKEEPER, User::ROLE_ASSISTANT]);
$clientApiRoles = 'role:'.implode(',', [User::ROLE_ADMIN, User::ROLE_ASSISTANT, User::ROLE_CLIENT]);
$clientOnly = 'role:'.User::ROLE_CLIENT;
$mechanicOnly = 'role:'.User::ROLE_MECHANIC;
$allRoles = 'role:'.implode(',', User::ROLES);

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', $allRoles])->name('dashboard');

Route::middleware(['auth', $adminOnly])->prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::patch('/{user}/status', [UserController::class, 'toggleStatus'])->name('toggleStatus');
});

Route::middleware(['auth', $managementRoles])->group(function () {
    Route::prefix('admin/clientes')->name('admin.clientes.')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/create', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/motos')->name('admin.motos.')->group(function () {
        Route::get('/', [MotoController::class, 'index'])->name('index');
        Route::get('/create/{cliente?}', [MotoController::class, 'create'])->name('create');
        Route::post('/{cliente?}', [MotoController::class, 'store'])->name('store');
        Route::get('/{moto}/edit', [MotoController::class, 'edit'])->name('edit');
        Route::put('/{moto}', [MotoController::class, 'update'])->name('update');
        Route::delete('/{moto}', [MotoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/citas')->name('admin.citas.')->group(function () {
        Route::get('/', [CitaController::class, 'index'])->name('index');
        Route::get('/create', [CitaController::class, 'create'])->name('create');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::get('/{cita}/edit', [CitaController::class, 'edit'])->name('edit');
        Route::put('/{cita}', [CitaController::class, 'update'])->name('update');
    });
});

Route::middleware(['auth', $adminOnly])->group(function () {
    Route::delete('/admin/citas/{cita}', [CitaController::class, 'destroy'])->name('admin.citas.destroy');
});

Route::middleware(['auth', $productReadRoles])->get('/productos', [ProductoController::class, 'index'])
    ->name('productos.index');

Route::middleware(['auth', $inventoryWriteRoles])->prefix('productos')->name('productos.')->group(function () {
    Route::get('/crear', [ProductoController::class, 'create'])->name('create');
    Route::post('/', [ProductoController::class, 'store'])->name('store');
    Route::get('/{id}/editar', [ProductoController::class, 'editar'])->name('editar');
    Route::put('/{id}', [ProductoController::class, 'actualizar'])->name('actualizar');
    Route::delete('/{id}', [ProductoController::class, 'eliminar'])->name('eliminar');
});

Route::middleware(['auth', $inventoryReadRoles])->group(function () {
    Route::get('/almacen', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/almacen/entradas', [EntradasController::class, 'index'])->name('entradas.index');
    Route::get('/inventario/salidas', [SalidasController::class, 'index'])->name('salidas.index');
});

Route::middleware(['auth', $inventoryWriteRoles])->group(function () {
    Route::get('/almacen/entradas/crear', [EntradasController::class, 'create'])->name('entradas.create');
    Route::post('/almacen/entradas/guardar', [EntradasController::class, 'store'])->name('entradas.store');
    Route::post('/almacen/registrar', [EntradasController::class, 'store'])->name('inventario.registrar');
    Route::get('/inventario/salidas/crear', [SalidasController::class, 'create'])->name('salidas.create');
    Route::post('/inventario/salidas/guardar', [SalidasController::class, 'store'])->name('salidas.store');
});

Route::middleware(['auth', $clientApiRoles])->get(
    '/api/clientes/{cliente}/motos',
    [MotoController::class, 'motosForCliente']
)->name('api.clientes.motos');

Route::middleware(['auth', $clientOnly])->group(function () {
    Route::get('/mis-motos', [MotoController::class, 'myIndex'])->name('mis-motos.index');
    Route::get('/mis-citas', [CitaController::class, 'index'])->name('mis-citas.index');
    Route::get('/mis-citas/crear', [CitaController::class, 'customerCreate'])->name('mis-citas.create');
    Route::post('/mis-citas', [CitaController::class, 'store'])->name('mis-citas.store');
    Route::patch('/mis-citas/{cita}/cancelar', [CitaController::class, 'cancelByClient'])->name('mis-citas.cancelar');
});

Route::middleware(['auth', $mechanicOnly])->group(function () {
    Route::get('/mecanico/citas', [CitaController::class, 'index'])->name('mecanico.citas.index');
    Route::patch('/mecanico/citas/{cita}/atender', [CitaController::class, 'attend'])->name('mecanico.citas.atender');
});

require __DIR__.'/auth.php';
