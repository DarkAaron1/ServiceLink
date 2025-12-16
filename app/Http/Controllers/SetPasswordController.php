<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SetPasswordController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        if (! $token) {
            // Si no viene token, permitir flujo por `email` (si viene en query)
            if ($email) {
                return view('Demo.set_password', ['token' => null, 'email' => $email]);
            }

            return redirect()->route('login')->withErrors(['token' => 'Token requerido.']);
        }

        $row = DB::table('password_set_tokens')->where('token', $token)->first();
        if (! $row || Carbon::parse($row->expires_at)->lt(now())) {
            return redirect()->route('login')->withErrors(['token' => 'Token inválido o expirado.']);
        }

        return view('Demo.set_password', ['token' => $token]);
    }

    public function store(Request $request)
    {
        // Aceptamos dos flujos:
        // - token (envío por email)
        // - rut (administrador estableciendo contraseña desde la UI)

        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        // Validar token si viene
        if ($request->filled('token')) {
            $rules['token'] = 'required|string';
        } else {
            // si no hay token, permitir email
            $rules['email'] = 'required|email';
        }

        $data = $request->validate($rules);

        try {
            if (! empty($data['token'])) {
                $row = DB::table('password_set_tokens')->where('token', $data['token'])->first();
                if (! $row || Carbon::parse($row->expires_at)->lt(now())) {
                    return back()->withErrors(['token' => 'Token inválido o expirado.']);
                }

                $email = $row->email;
                $type = $row->type; // 'usuario' o 'empleado'

                if ($type === 'usuario') {
                    $usuario = Usuario::where('email', $email)->first();
                    if (! $usuario) return back()->withErrors(['email' => 'Usuario no encontrado.']);
                    $usuario->password = $data['password']; // mutator hasheará
                    $usuario->save();
                } else {
                    // actualizar empleado por email
                    DB::table('empleados')->where('email', $email)->update([
                        'password' => Hash::make($data['password']),
                        'updated_at' => now(),
                    ]);
                }

                // Invalidar token si existe la tabla
                try {
                    DB::table('password_set_tokens')->where('token', $data['token'])->delete();
                } catch (\Exception $e) {
                    // ignorar si la tabla no existe
                }

                return redirect()->route('login')->with('success', 'Contraseña guardada correctamente. Ya puedes iniciar sesión.');
            } else {
                // Flujo por email: actualizar el registro correspondiente al email proporcionado
                $email = $data['email'];

                // Intentar actualizar empleado por email
                $empleado = DB::table('empleados')->where('email', $email)->first();
                if ($empleado) {
                    DB::table('empleados')->where('email', $email)->update([
                        'password' => Hash::make($data['password']),
                        'updated_at' => now(),
                    ]);
                    return redirect()->route('empleados.index')->with('success', 'Contraseña del colaborador actualizada correctamente.');
                }

                // Intentar actualizar usuario por email
                $usuario = Usuario::where('email', $email)->first();
                if ($usuario) {
                    $usuario->password = $data['password'];
                    $usuario->save();
                    return redirect()->route('admin.index')->with('success', 'Contraseña del usuario actualizada correctamente.');
                }

                return back()->withErrors(['email' => 'Registro no encontrado (ni empleado ni usuario).']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['db' => 'Error al guardar la contraseña: ' . $e->getMessage()]);
        }
    }
}