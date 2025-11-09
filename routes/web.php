<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MesasController;

Route::get('/', function () {
    return view('login');
});

Route::get('/newhome',function(){
    return view('Demo.index');
}); 

Route::get('/login',function(){
    return view('login');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

// ruta de bienvenida (a donde redirige el login)
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

// pantalla de administración de usuarios
Route::get('/admin', [UsuariosController::class, 'index'])->name('admin.index');

// recursos mínimos para usuarios (crear, mostrar, actualizar estado)
Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
Route::get('/usuarios/{usuarios}', [UsuariosController::class, 'show'])->name('usuarios.show');
Route::patch('/usuarios/{usuarios}', [UsuariosController::class, 'update'])->name('usuarios.update');

// rutas para mesas
Route::get('/mesas', [MesasController::class, 'index'])->name('mesas.index');
Route::post('/mesas', [MesasController::class, 'store'])->name('mesas.store');
Route::patch('/mesas/{id}', [MesasController::class, 'update'])->name('mesas.update');
Route::delete('/mesas/{mesa}', [MesasController::class, 'destroy'])->name('mesas.destroy');