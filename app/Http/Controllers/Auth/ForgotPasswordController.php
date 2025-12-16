<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Exception;


class ForgotPasswordController extends Controller
{

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');

        $foundInUsers = DB::table('usuarios')->where('email', $email)->exists();
        $foundInEmpleados = DB::table('empleados')->where('email', $email)->exists();

        if (! $foundInUsers && ! $foundInEmpleados) {
            return back()->withErrors(['email' => 'No se encontró ese correo en nuestros registros.'])->withInput();
        }

        // Obtener el registro (usuarios primero, luego empleados)
        $record = null;
        if ($foundInUsers) {
            $record = DB::table('usuarios')->where('email', $email)->first();
        } elseif ($foundInEmpleados) {
            $record = DB::table('empleados')->where('email', $email)->first();
        }

        // Extraer nombre y apellido probando varias columnas comunes
        $nombre = '';
        $apellido = '';
        if ($record) {
            $nombre = $record->nombre ?? $record->name ?? $record->first_name ?? '';
            $apellido = $record->apellido ?? $record->last_name ?? $record->apellido_paterno ?? $record->apellido_materno ?? '';
        }

        $datos = [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
        ];

        // Generar token y, si existe la tabla, guardarlo para validación posterior
        $token = bin2hex(random_bytes(32));
        $url = route('set-password');

        try {
            if (Schema::hasTable('password_set_tokens')) {
                DB::table('password_set_tokens')->insert([
                    'email' => $email,
                    'type' => $foundInUsers ? 'usuario' : 'empleado',
                    'token' => $token,
                    'expires_at' => now()->addHours(24),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $url = route('set-password', ['token' => $token]);
            } else {
                // fallback: enviar link con email en query
                $url = route('set-password', ['email' => $email]);
            }
        } catch (Exception $e) {
            // Si falla al escribir token, fallback a link con email
            $url = route('set-password', ['email' => $email]);
        }

        $html = "<!doctype html>
			<html lang='es'><head><meta charset='utf-8'><title>ServiceLink</title></head><body>
			<h2>ServiceLink</h2>
			<p>Hola " . e(trim($datos['nombre'] . ' ' . $datos['apellido'])) . ",</p>
			<p>Has solicitado restablecer tu contraseña para la cuenta asociada a: " . e($datos['email']) . ".</p>
			<p>Si no solicitaste este correo, ignora este mensaje.</p>
			<p>Saludos,<br>Equipo ServiceLink</p>
            <p>
                <a href=" . $url . ">Restablecer contraseña</a>
            </p>
			</body></html>";

        Mail::send([], [], function ($message) use ($email, $html) {
            $message->to($email)
                ->subject('ServiceLink — Mensaje solicitado')
                ->html($html);
        });

        return back()->with('status', 'Se ha enviado el mensaje al correo proporcionado.');
    }
}
