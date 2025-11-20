<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Si no hay sesión, redirigir al login
        $rut = $request->session()->get('usuario_rut');
        if (! $rut) {
            return redirect()->route('login');
        }

        // Intentar cargar usuario desde DB; si no existe, usar valores en sesión como fallback
        $usuario = Usuario::where('rut', $rut)->first();
        if (! $usuario) {
            $usuario = (object) [
                'nombre' => $request->session()->get('usuario_nombre'),
                'email' => $request->session()->get('usuario_email'),
                'rol_id' => null,
                'estado' => null,
            ];
        }

        // Obtener nombre del rol si aplica
        $rolName = null;
        if (! empty($usuario->rol_id)) {
            $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
        }

        return view('Demo.index', compact('usuario', 'rolName'));
    }
}
