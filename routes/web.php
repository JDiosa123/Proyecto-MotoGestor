<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

     Route::patch('/admin/users/{user}/status', [UserController::class, 'toggleStatus'])
          ->name('admin.users.toggleStatus');

      // Administración de clientes (CRUD completo)
    Route::get('/admin/clientes', [ClienteController::class, 'index'])->name('admin.clientes.index');
    Route::get('/admin/clientes/create', [ClienteController::class, 'create'])->name('admin.clientes.create');
    Route::post('/admin/clientes', [ClienteController::class, 'store'])->name('admin.clientes.store');
    Route::get('/admin/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('admin.clientes.edit');
    Route::put('/admin/clientes/{cliente}', [ClienteController::class, 'update'])->name('admin.clientes.update');
    Route::delete('/admin/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('admin.clientes.destroy'); 
});

require __DIR__.'/auth.php';
