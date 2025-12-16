<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsCategoriaController;
use App\Http\Controllers\ItemsMenuController;
use App\Http\Controllers\MesasController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LoginController; // agregado
use App\Http\Controllers\DashboardController; // agregado
use App\Http\Controllers\EmpleadoController; // <-- agregado
use App\Http\Controllers\RestauranteController; // <-- agregado
use App\Http\Controllers\ComandaController;
use App\Mail\MiPrimerEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/index', [DashboardController::class, 'index'])->name('demo.index');

// Pantalla de selección: elegir login para Usuario o Empleado
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

// Login para Usuario
Route::get('/login/usuario', [LoginController::class, 'showUsuarioLogin'])->name('login.usuario');
Route::post('/login/usuario', [LoginController::class, 'authenticateUsuario'])->name('login.usuario.perform');

// Login para Empleado
Route::get('/login/empleado', [LoginController::class, 'showEmpleadoLogin'])->name('login.empleado');
Route::post('/login/empleado', [LoginController::class, 'authenticateEmpleado'])->name('login.empleado.perform');

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
Route::get('/mesas/{mesa}', [MesasController::class, 'show'])->name('mesas.show');
Route::post('/mesas', [MesasController::class, 'store'])->name('mesas.store');
Route::patch('/mesas/{id}', [MesasController::class, 'update'])->name('mesas.update');
Route::delete('/mesas/{mesa}', [MesasController::class, 'destroy'])->name('mesas.destroy');

// Rutas para comandas
Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
Route::get('/comandas/{comanda}', [ComandaController::class, 'show'])->name('comandas.show');

// Rutas para items menú
Route::get('/items_menus', [ItemsMenuController::class, 'index'])->name('items_menu.index');
Route::post('/items_menus', [ItemsMenuController::class, 'store'])->name('items_menus.store');
Route::get('/items_menus/{items_Menu}', [ItemsMenuController::class, 'show'])->name('items_menus.show');
Route::patch('/items_menus/{items_Menu}', [ItemsMenuController::class, 'update'])->name('items_menus.update');
Route::delete('/items_menus/{items_menu}', [ItemsMenuController::class, 'destroy'])->name('items_menus.destroy');

// Filtrar por categoría
Route::get('/items_menus/categoria/{categoria}', [ItemsMenuController::class, 'byCategoria'])->name('items_menus.byCategoria');

// Ruta pública para mostrar la carta/menu al cliente
// Ahora acepta un id de restaurante: /carta/{restauranteId}
Route::get('/carta/{restaurante}', [ItemsMenuController::class, 'verCarta'])->name('menu.carta');

Route::get('/categorias', [ItemsCategoriaController::class, 'index'])->name('categorias.index');
Route::post('/items_categorias', [ItemsCategoriaController::class, 'store'])->name('items_categorias.store');
Route::delete('/items_categorias/{items_Categoria}', [ItemsCategoriaController::class, 'destroy'])->name('items_categorias.destroy');
Route::patch('/items_categorias/{items_Categoria}', [ItemsCategoriaController::class, 'update'])->name('items_categorias.update');

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

//Ruta QR
Route::get('/qr/{restaurante}', [App\Http\Controllers\EndroidQrCodeController::class, 'generateQrCode'])->name('generate.qr');

//Ruta cocina
Route::get('/cocina', [App\Http\Controllers\PedidoController::class, 'index'])->name('cocina.index');
// Endpoint para comprobar si hay nuevas órdenes (usa en polling cliente)
Route::get('/cocina/ordenes/latest', [App\Http\Controllers\PedidoController::class, 'latestOrder'])->name('cocina.ordenes.latest');
