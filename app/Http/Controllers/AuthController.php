<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuarios;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Procesa el login y redirige a la ruta 'welcome' si las credenciales son correctas.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = usuarios::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // para este ejemplo simple, no se implementa sesión completa;
            // se puede setear sesión básica para identificar al usuario
            session(['usuario_run' => $user->run, 'usuario_nombre' => $user->nombre]);
            return redirect()->route('welcome');
        }

        return back()->withErrors(['email' => 'Credenciales inválidas.'])->withInput();
    }

    public function index()
    {
        //
    }
}