<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Empleado;

class LoginController extends Controller
{
    // Mostrar pantalla de selección de tipo de login
    public function showLoginForm(Request $request)
    {
        // Limpiar cualquier dato de autenticación previo (usuario o empleado)
        $request->session()->forget(['usuario_rut','usuario_nombre','usuario_email','empleado_rut','empleado_nombre','empleado_email','empleado_cargo']);
        // Renovar token CSRF para evitar reuso de formularios antiguos
        $request->session()->regenerateToken();

        return view('auth.login_select');
    }

    // Mostrar formulario de login para Usuario
    public function showUsuarioLogin(Request $request)
    {
        // Limpiar sesión de empleado por si había quedado algún dato
        $request->session()->forget(['empleado_rut','empleado_nombre','empleado_email','empleado_cargo']);
        $request->session()->regenerateToken();

        return view('Demo.login');
    }

    // Mostrar formulario de login para Empleado
    public function showEmpleadoLogin(Request $request)
    {
        // Limpiar sesión de usuario por si había quedado algún dato
        $request->session()->forget(['usuario_rut','usuario_nombre','usuario_email']);
        $request->session()->regenerateToken();

        return view('auth.login_empleado');
    }

    // Procesar intento de login para Usuario
    public function authenticateUsuario(Request $request)
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

    // Procesar intento de login para Empleado
    public function authenticateEmpleado(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $empleado = \App\Models\Empleado::where('email', $credentials['email'])->first();

        if (! $empleado || ! Hash::check($credentials['password'], $empleado->password)) {
            return back()->withInput($request->only('email'))->withErrors(['auth' => 'Credenciales inválidas']);
        }

        // Iniciar sesión de forma simple guardando identificador del empleado en sesión
        $request->session()->regenerate();
        $request->session()->put('empleado_rut', $empleado->rut);
        $request->session()->put('empleado_nombre', $empleado->nombre);
        $request->session()->put('empleado_email', $empleado->email);
        $request->session()->put('empleado_cargo', $empleado->cargo ?? '');

        // Redirigir según cargo del empleado: Cocinero -> Cocina, Mesero -> Comandas, Administrador -> Dashboard
        $cargo = strtolower(trim($empleado->cargo ?? ''));
        if ($cargo === 'cocinero') {
            return redirect()->route('cocina.index');
        }

        if ($cargo === 'mesero') {
            return redirect()->route('comandas.index');
        }

        // Administrador o cualquier otro cargo por defecto: dashboard
        return redirect()->route('demo.index');
    }

    // Cerrar sesión (limpia sesiones de usuario y empleado)
    public function logout(Request $request)
    {
        $request->session()->forget(['usuario_rut','usuario_nombre','usuario_email','empleado_rut','empleado_nombre','empleado_email','empleado_cargo']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
