<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;


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

        $token = Str::random(60);
        $url = route('set-password');

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
