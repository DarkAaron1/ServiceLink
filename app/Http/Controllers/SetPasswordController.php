<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


class SetPasswordController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->route('token') ?? $request->query('token');
        $email = $request->query('email');

        // Log what the server sees when rendering the form (temporal)
        try {
            Log::info('set-password.show', [
                'token_param' => $request->route('token'),
                'token_query' => $request->query('token'),
                'computed_token' => $token,
                'email_query' => $email,
                'url' => $request->fullUrl()
            ]);
        } catch (\Exception $e) {
            // ignore logging errors
        }

        // Caso: sin token
        if (! $token) {
            if ($email) {
                // Si existe la tabla de tokens, intentar generar (o reutilizar) un token
                try {
                    if (Schema::hasTable('password_set_tokens')) {
                        $existing = DB::table('password_set_tokens')
                            ->where('email', $email)
                            ->whereRaw('expires_at > ?', [now()])
                            ->first();

                        if ($existing) {
                            $token = $existing->token;
                        } else {
                            $token = bin2hex(random_bytes(32));
                            DB::table('password_set_tokens')->insert([
                                'email' => $email,
                                'type' => 'usuario',
                                'token' => $token,
                                'expires_at' => now()->addHours(24),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        // Redirigir a la misma ruta pero incluyendo el token en la ruta
                        return redirect()->route('set-password', ['token' => $token, 'email' => $email]);
                    }
                } catch (\Exception $e) {
                    // Si algo falla al generar el token, caer al render sin token
                }

                return view('Demo.set_password', [
                    'token' => null,
                    'email' => $email
                ]);
            }

            return view('Demo.set_password', [
                'token' => null,
                'email' => null,
                'error' => 'Token requerido.'
            ]);
        }

        $row = DB::table('password_set_tokens')->where('token', $token)->first();

        // Caso: token inválido o expirado
        if (! $row || Carbon::parse($row->expires_at)->lt(now())) {
            return view('Demo.set_password', [
                'token' => null,
                'email' => null,
                'error' => 'Token inválido o expirado.'
            ]);
        }

        // Caso OK
        return view('Demo.set_password', [
            'token' => $token,
            'email' => $row->email ?? null
        ]);
    }

    public function store(Request $request)
    {
        // Aceptar token también desde la ruta o query si no viene en el body
        $incomingToken = $request->input('token') ?? $request->route('token') ?? $request->query('token');
        try {
            Log::info('set-password.store.start', [
                'incomingToken_before_merge' => $request->input('token'),
                'route_token' => $request->route('token'),
                'query_token' => $request->query('token'),
                'full_url' => $request->fullUrl()
            ]);
        } catch (\Exception $e) {
            // ignore
        }
        if ($incomingToken && ! $request->filled('token')) {
            $request->merge(['token' => $incomingToken]);
        }

        // Aceptamos dos flujos:
        // - token (envío por email)
        // - email (administrador estableciendo contraseña desde la UI)

        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        // Validar token si viene
        if ($request->input('token')) {
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

                // Primero intentar actualizar en `empleados` por email
                $empleado = DB::table('empleados')->where('email', $email)->first();
                if ($empleado) {
                    DB::table('empleados')->where('email', $email)->update([
                        'password' => Hash::make($data['password']),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Si no existe empleado, intentar actualizar en usuarios
                    $usuario = Usuario::where('email', $email)->first();
                    if (! $usuario) return back()->withErrors(['email' => 'Registro no encontrado.']);
                    $usuario->password = Hash::make($data['password']);
                    $usuario->save();
                }

                // Invalidar token si existe la tabla
                try {
                    DB::table('password_set_tokens')->where('token', $data['token'])->delete();
                } catch (\Exception $e) {
                    // ignorar si la tabla no existe
                }

                return view('Demo.set_password_success');
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
                    $usuario->password = Hash::make($data['password']);
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
