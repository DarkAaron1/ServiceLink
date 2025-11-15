<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurante;
use App\Models\Usuario;

class RestauranteController extends Controller
{
    // Mostrar formulario de creación
    public function create()
    {
        // Opcional: obtener lista de administradores (usuarios)
        $admins = Usuario::all(['rut', 'nombre', 'apellido', 'email']);
        return view('Demo.restaurante_create', compact('admins'));
    }

    // Guardar restaurante
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:restaurantes,nombre'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:50', 'unique:restaurantes,telefono'],
            'email' => ['required', 'email', 'max:255', 'unique:restaurantes,email'],
            'fecha_creacion' => ['nullable', 'date'],
            'rut_admin' => ['required', 'string', 'exists:usuarios,rut'],
        ], [
            'rut_admin.exists' => 'El administrador con RUT ingresado no existe en el sistema.',
        ]);

        Restaurante::create($data);

        return redirect()->route('restaurante.create')->with('success', 'Restaurante creado correctamente.');
    }
}
