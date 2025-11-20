<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsMenuController;
use App\Http\Controllers\MesasController;

// Página principal (login)
Route::get('/', function () {
    return view('Demo.index');
});


Route::get('/login',function(){
    return view('Demo.login');
});

Route::get('/register',function(){
    return view('Demo.register');
});

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
Route::get('/usuarios/{usuarios}', [UsuariosController::class, 'show'])->name('usuarios.show');
Route::patch('/usuarios/{usuarios}', [UsuariosController::class, 'update'])->name('usuarios.update');

// rutas para mesas
Route::get('/mesas', [MesasController::class, 'index'])->name('mesas.index');
Route::post('/mesas', [MesasController::class, 'store'])->name('mesas.store');
Route::patch('/mesas/{id}', [MesasController::class, 'update'])->name('mesas.update');
Route::delete('/mesas/{mesa}', [MesasController::class, 'destroy'])->name('mesas.destroy');

// Rutas para items menú
Route::get('/items_menus', [ItemsMenuController::class, 'index'])->name('items_Menu.index');
Route::post('/items_menus', [ItemsMenuController::class, 'store'])->name('items_menus.store');
