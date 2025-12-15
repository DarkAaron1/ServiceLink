<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class LoginController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('Demo.login');
    }

    // Procesar intento de login
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $usuario = Usuario::where('email', $credentials['email'])->first();

        if (! $usuario || ! Hash::check($credentials['password'], $usuario->password)) {
            return back()->withInput($request->only('email'))->withErrors(['auth' => 'Credenciales inválidas']);
        }

        // Iniciar sesión de forma simple guardando identificador en sesión
        $request->session()->regenerate();
        $request->session()->put('usuario_rut', $usuario->rut);
        $request->session()->put('usuario_nombre', $usuario->nombre);
        $request->session()->put('usuario_email', $usuario->email);

        // redirigir a la vista Demo.index mediante la ruta nombrada 'demo.index'
        return redirect()->route('demo.index');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        $request->session()->forget(['usuario_rut','usuario_nombre','usuario_email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
