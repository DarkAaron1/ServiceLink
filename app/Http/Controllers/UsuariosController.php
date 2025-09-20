<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = usuarios::orderBy('created_at', 'desc')->get();
        return view('admin', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'run' => 'required|string|unique:usuarios,run',
            'username' => 'required|string|unique:usuarios,username',
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6|confirmed',
            'nacimiento' => 'required|date',
            'telefono' => 'nullable|numeric',
            'estado' => 'nullable|in:on',
        ]);

        $user = new usuarios();
        $user->run = $data['run'];
        $user->username = $data['username'];
        $user->nombre = $data['nombre'];
        $user->apellido = $data['apellido'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->nacimiento = $data['nacimiento'];
        $user->telefono = $data['telefono'] ?? null;
        $user->estado = isset($data['estado']) ? 1 : 0;
        $user->save();

        return redirect()->route('admin.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(usuarios $usuarios)
    {
        return response()->json($usuarios);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(usuarios $usuarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Aquí se usa para habilitar/inhabilitar usuario (toggle estado).
     */
    public function update(Request $request, usuarios $usuarios)
    {
        // toggle estado si se envía acción
        if ($request->has('toggle_estado')) {
            $usuarios->estado = $usuarios->estado ? 0 : 1;
            $usuarios->save();
            return redirect()->route('admin.index')->with('success', 'Estado actualizado.');
        }

        // opción para actualizar otros campos (no requerida ahora)
        $data = $request->validate([
            'username' => ['sometimes','required', Rule::unique('usuarios','username')->ignore($usuarios->run,'run')],
            'email' => ['sometimes','required','email', Rule::unique('usuarios','email')->ignore($usuarios->run,'run')],
            'nombre' => 'sometimes|required|string',
            'apellido' => 'sometimes|required|string',
        ]);

        $usuarios->update($data);
        return redirect()->route('admin.index')->with('success', 'Usuario actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(usuarios $usuarios)
    {
        //
    }
}
