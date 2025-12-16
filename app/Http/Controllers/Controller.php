<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Empleado;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    /**
     * Devuelve el actor autenticado desde sesión: Usuario o Empleado
     * Retorna null si no hay ninguno.
     * Devuelve un array con keys: type, model, rut, nombre, email, rolName, restaurante_id
     */
    protected function getActor(Request $request)
    {
        $empleadoRut = $request->session()->get('empleado_rut');
        if ($empleadoRut) {
            $empleado = Empleado::where('rut', $empleadoRut)->first();
            $nombre = $request->session()->get('empleado_nombre') ?? ($empleado->nombre ?? null);
            $email = $request->session()->get('empleado_email') ?? ($empleado->email ?? null);
            $cargo = $request->session()->get('empleado_cargo') ?? ($empleado->cargo ?? null);

            return [
                'type' => 'empleado',
                'model' => $empleado,
                'rut' => $empleadoRut,
                'nombre' => $nombre,
                'email' => $email,
                'rolName' => $cargo,
                'restaurante_id' => $empleado->restaurante_id ?? null,
            ];
        }

        $usuarioRut = $request->session()->get('usuario_rut');
        if ($usuarioRut) {
            $usuario = Usuario::where('rut', $usuarioRut)->first();
            $nombre = $request->session()->get('usuario_nombre') ?? ($usuario->nombre ?? null);
            $email = $request->session()->get('usuario_email') ?? ($usuario->email ?? null);
            $rolName = null;
            if ($usuario && ! empty($usuario->rol_id)) {
                $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
            }

            return [
                'type' => 'usuario',
                'model' => $usuario,
                'rut' => $usuarioRut,
                'nombre' => $nombre,
                'email' => $email,
                'rolName' => $rolName,
                'restaurante_id' => null,
            ];
        }

        return null;
    }
}
