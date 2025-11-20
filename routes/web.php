<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsCategoriaController;
use App\Http\Controllers\ItemsMenuController;
use App\Http\Controllers\MesasController;

Route::get('/', function () {
    return view('Demo.index');
});


Route::get('/login',function(){
    return view('Demo.login');
});

Route::get('/register',function(){
    return view('Demo.register');
});

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

// Rutas para items menú
Route::get('/items_menus', [ItemsMenuController::class, 'index'])->name('items_menu.index');
Route::post('/items_menus', [ItemsMenuController::class, 'store'])->name('items_menus.store');
Route::get('/items_menus/{items_Menu}', [ItemsMenuController::class, 'show'])->name('items_menus.show');
Route::patch('/items_menus/{items_Menu}', [ItemsMenuController::class, 'update'])->name('items_menus.update');
Route::delete('/items_menus/{items_menu}', [ItemsMenuController::class, 'destroy'])->name('items_menus.destroy');
// Filtrar por categoría
Route::get('/items_menus/categoria/{categoria}', [ItemsMenuController::class, 'byCategoria'])->name('items_menus.byCategoria');

Route::get('/categorias', [ItemsCategoriaController::class, 'index'])->name('categorias.index');
Route::post('/items_categorias', [ItemsCategoriaController::class, 'store'])->name('items_categorias.store');
Route::delete('/items_categorias/{items_Categoria}', [ItemsCategoriaController::class, 'destroy'])->name('items_categorias.destroy');
Route::patch('/items_categorias/{items_Categoria}', [ItemsCategoriaController::class, 'update'])->name('items_categorias.update');
