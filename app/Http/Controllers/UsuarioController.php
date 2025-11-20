<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class UsuarioController extends Controller
{
    public function ver(){
        return view('Demo.register');
    }
    // Mostrar formulario de registro
    public function create()
    {
        // Traer roles para el select (si existen)
        $roles = DB::table('roles')->select('id', 'nombre')->get();

        return view('Demo.register', compact('roles'));
    }

    // Almacenar nuevo usuario
    public function store(Request $request)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:50', 'unique:usuarios,rut'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'fecha_nacimiento' => ['required', 'date'],
            'rol_id' => ['required', 'integer', 'exists:roles,id'],
            'estado' => ['sometimes', 'in:activo,inactivo'],
        ]);

        try {
            // Crear usuario (el mutator del modelo hashea el password si corresponde)
            $usuario = Usuario::create([
                'rut' => $data['rut'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'password' => $data['password'], // mutator se encargará de hashear si procede
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'rol_id' => $data['rol_id'],
                'estado' => $data['estado'] ?? 'inactivo',
            ]);
        } catch (Exception $e) {
            // Log opcional: \Log::error($e);
            return back()->withInput()->withErrors(['db' => 'Ocurrió un error al crear la cuenta. ' . $e->getMessage()]);
        }

        return redirect()->route('login')->with('success', 'Cuenta creada correctamente. Verifica tu correo si aplica.');
    }
}
