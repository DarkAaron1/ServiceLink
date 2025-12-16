<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Si hay sesión de Usuario o Empleado, mostrar dashboard; si no, redirigir al selector de login
        $usuario = null;
        $rolName = null;

        // Preferir usuario (cliente) si existe
        $usuarioRut = $request->session()->get('usuario_rut');
        $empleadoRut = $request->session()->get('empleado_rut');

        if ($usuarioRut) {
            $usuario = Usuario::where('rut', $usuarioRut)->first();
            if (! $usuario) {
                $usuario = (object) [
                    'nombre' => $request->session()->get('usuario_nombre'),
                    'email' => $request->session()->get('usuario_email'),
                    'rol_id' => null,
                    'estado' => null,
                ];
            }

            if (! empty($usuario->rol_id)) {
                $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
            }
        } elseif ($empleadoRut) {
            // Mostrar dashboard para empleado
            $empleado = \App\Models\Empleado::where('rut', $empleadoRut)->first();
            if (! $empleado) {
                $usuario = (object) [
                    'nombre' => $request->session()->get('empleado_nombre'),
                    'email' => $request->session()->get('empleado_email'),
                    'rol_id' => null,
                    'estado' => null,
                ];
                $rolName = $request->session()->get('empleado_cargo');
            } else {
                $usuario = $empleado;
                $rolName = $empleado->cargo ?? null;
            }
        } else {
            return redirect()->route('login');
        }

        return view('Demo.index', compact('usuario', 'rolName'));
    }
}
