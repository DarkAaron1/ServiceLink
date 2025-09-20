<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;

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
