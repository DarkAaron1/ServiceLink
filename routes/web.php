<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\AuthController;

// Página principal (login)
Route::get('/', function () {
    return view('login');
});

// Página de login
Route::get('/login', function () {
    return view('login');
});
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Página de bienvenida
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// Página de ejemplo
Route::get('/newhome', function () {
    return view('Demo.index');
});

// Administración de usuarios (dashboard)
Route::get('/admin', [UsuariosController::class, 'index'])->name('admin.index');

// CRUD de usuarios
Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
Route::get('/usuarios/crear', [UsuariosController::class, 'create'])->name('usuarios.create');
Route::get('/usuarios/{usuario}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit');
Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
Route::patch('/usuarios/{usuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{usuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

// CRUD de comandas 
Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
Route::get('/comandas/crear', [ComandaController::class, 'create'])->name('comandas.create');
Route::get('/comandas/{comanda}/editar', [ComandaController::class, 'edit'])->name('comandas.edit');
Route::get('/comandas/{comanda}/form', [ComandaController::class, 'getEditForm'])->name('comandas.form');
Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
Route::patch('/comandas/{comanda}', [ComandaController::class, 'update'])->name('comandas.update');
Route::delete('/comandas/{comanda}', [ComandaController::class, 'destroy'])->name('comandas.destroy');

