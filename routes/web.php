<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsMenuController;
use App\Http\Controllers\MesasController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController; // agregado
use App\Http\Controllers\DashboardController; // agregado
use App\Http\Controllers\EmpleadoController; // <-- agregado
use App\Http\Controllers\RestauranteController; // <-- agregado
use App\Mail\MiPrimerEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/index', [DashboardController::class, 'index'])->name('index');

// Reemplazado: mostrar login mediante controlador
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Procesar login
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');
// Logout (GET para compatibilidad con enlaces simples en UI)
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Formulario de registro público
Route::get('/register', [UsuarioController::class, 'create'])->name('register');
// Cambiado el nombre de la ruta POST para evitar conflicto con 'usuarios.store'
Route::post('/register', [UsuarioController::class, 'store'])->name('register.store');

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

// Rutas para items menú
Route::get('/items_menus', [ItemsMenuController::class, 'index'])->name('items_Menu.index');
Route::post('/items_menus', [ItemsMenuController::class, 'store'])->name('items_menus.store');

// Rutas para colaboradores (empleados)
Route::get('/colaboradores', [EmpleadoController::class, 'index'])->name('empleados.index');
Route::post('/colaboradores', [EmpleadoController::class, 'store'])->name('empleados.store');
Route::put('/colaboradores/{rut}', [EmpleadoController::class, 'update'])->name('empleados.update');
Route::delete('/colaboradores/{rut}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');

// Rutas para crear y almacenar un restaurante
Route::get('restaurante/create', [RestauranteController::class, 'create'])->name('restaurante.create');
Route::post('restaurante/store', [RestauranteController::class, 'store'])->name('restaurante.store');

//Ruta para contraseñas
Route::post('/colaboradores/{rut}/reset-password', [EmpleadoController::class, 'reestablecerContrasena'])->name('empleados.reset_password');

