<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuariosController extends Controller
{
    /**
     * Muestra todos los usuarios.
     */
    public function index()
    {
        $usuarios = Usuario::orderBy('created_at', 'desc')->get();
        return view('Usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('Usuarios.create');
    }

    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit(Usuario $usuario)
    {
        return view('Usuarios.edit', compact('usuario'));
    }

    /**
     * Guarda un nuevo usuario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'rut' => 'required|string|unique:usuarios,rut',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'fecha_nacimiento' => 'required|date',
            'fecha_creacion' => 'nullable|date',
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'nullable|boolean',
        ]);

        $usuario = new Usuario();
        $usuario->rut = $data['rut'];
        $usuario->nombre = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->email = $data['email'];
        $usuario->password = Hash::make($data['password']);
        $usuario->fecha_nacimiento = $data['fecha_nacimiento'];
        $usuario->estado = isset($data['estado']) ? 1 : 0;
        $usuario->rol_id = $data['rol_id'];
        if (!empty($data['fecha_creacion'])) {
            $usuario->created_at = $data['fecha_creacion'];
        }
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Muestra un usuario específico.
     */
    public function show(Usuario $usuario)
    {
        return response()->json($usuario);
    }

    /**
     * Actualiza un usuario o cambia su estado.
     */
    public function update(Request $request, Usuario $usuario)
    {
        // Toggle de estado
        if ($request->has('toggle_estado')) {
            $usuario->estado = !$usuario->estado;
            $usuario->save();
            return redirect()->route('usuarios.index')->with('success', 'Estado actualizado.');
        }

        // Actualización normal
        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellido' => 'sometimes|required|string|max:100',
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('usuarios', 'email')->ignore($usuario->rut, 'rut'),
            ],
            'fecha_creacion' => 'sometimes|date',
            'rol_id' => 'sometimes|required|exists:roles,id',
        ]);

        // Manejar fecha de creación por separado 
        $fechaCreacion = null;
        if (isset($data['fecha_creacion'])) {
            $fechaCreacion = $data['fecha_creacion'];
            unset($data['fecha_creacion']);
        }

        $usuario->update($data);

        if ($fechaCreacion) {
            $usuario->created_at = $fechaCreacion;
            $usuario->save();
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
