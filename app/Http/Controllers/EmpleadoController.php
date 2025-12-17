<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Usuario; // agregado
use Illuminate\Support\Facades\Mail;
use App\Mail\MiPrimerEmail;
use App\Mail\EmpleadoBienvenidaMail;
use Illuminate\Support\Facades\Schema;

class EmpleadoController extends Controller
{
    // Mostrar listado y formulario (roles/restaurantes para select en modal)
    public function index()
    {
        // Obtener empleados (simple listado)
        $empleados = DB::table('empleados')->select('rut', 'nombre', 'apellido', 'email', 'fono', 'fecha_nacimiento', 'cargo', 'estado', 'restaurante_id')->get();

        // Para selects en el modal
        $roles = DB::table('roles')->select('nombre')->get();

        // --- NUEVO: obtener datos del usuario para la sección "Profile" ---
        // Intentar cargar usuario desde sesión (misma lógica que DashboardController)
        $rutSesion = request()->session()->get('usuario_rut');
        $usuario = null;
        if ($rutSesion) {
            $usuario = Usuario::where('rut', $rutSesion)->first();
        }

        // Si no se obtuvo desde sesión, intentar con el usuario autenticado
        if (! $usuario) {
            $userAuth = Auth::user();
            if ($userAuth) {
                // Si existe el modelo Usuario y coincide por rut, usarlo; si no, usar el objeto auth como fallback
                $usuario = Usuario::where('rut', $userAuth->rut)->first() ?? $userAuth;
            } else {
                // fallback a valores de sesión si no hay Auth
                $usuario = (object) [
                    'nombre' => request()->session()->get('usuario_nombre'),
                    'email' => request()->session()->get('usuario_email'),
                    'rut' => $rutSesion,
                    'rol_id' => null,
                    'estado' => null,
                ];
            }
        }

        // Obtener nombre del rol si aplica
        $rolName = null;
        if (! empty($usuario->rol_id)) {
            $rolName = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
        } elseif (isset($usuario->rol) && is_object($usuario->rol) && isset($usuario->rol->nombre)) {
            $rolName = $usuario->rol->nombre;
        } elseif (isset($usuario->role) && is_object($usuario->role) && isset($usuario->role->name)) {
            $rolName = $usuario->role->name;
        } elseif (isset($usuario->roles) && is_iterable($usuario->roles)) {
            $first = collect($usuario->roles)->first();
            if ($first) $rolName = $first->nombre ?? $first->name ?? null;
        }

        // Retornar la vista incluyendo usuario y rolName
        return view('colaboradores.index', compact('empleados', 'roles', 'usuario', 'rolName'));
    }

    // Almacenar nuevo empleado
    public function store(Request $request)
    {
        // Normalizar RUT: remover puntos y convertir a formato estándar
        $rutInput = $request->input('rut');
        $rutNormalizado = $this->normalizarRUT($rutInput);

        // Validar formato y dígito verificador del RUT antes de continuar
        if (! $this->validarRUT($rutNormalizado)) {
            return back()->withInput()->withErrors(['rut' => 'RUT inválido']);
        }

        $data = $request->validate([
            'rut' => ['required', 'string', 'max:50'],
            // nombres solo letras y espacios (unicode)
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            // teléfono solo dígitos
            'fono' => ['required', 'regex:/^[0-9]+$/', 'max:15'],
            // Validación RFC y DNS para asegurar que el dominio existe
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:empleados,email'],
            'cargo' => ['required', 'string', 'exists:roles,nombre'],
            'estado' => ['sometimes', 'in:activo,inactivo'],
        ]);

        // Validar que el RUT no exista (normalizado)
        $rutExistente = DB::table('empleados')
            ->where('rut', $rutNormalizado)
            ->first();

        if ($rutExistente) {
            return back()->withInput()->withErrors(['rut' => 'El RUT ya está registrado']);
        }

        // Verificar que el dominio del correo tenga registros MX o A (existencia del dominio)
        $emailDomain = substr(strrchr($data['email'], '@'), 1);
        if ($emailDomain === false || (!checkdnsrr($emailDomain, 'MX') && !checkdnsrr($emailDomain, 'A'))) {
            return back()->withInput()->withErrors(['email' => 'Dominio del correo no válido o no existe.']);
        }

        // Intentar verificar existencia del buzón vía SMTP (RCPT TO)
        if (! $this->verificarEmailSMTP($data['email'])) {
            return back()->withInput()->withErrors(['email' => 'Email inválido o no existente.']);
        }

        try {
            // Obtener el restaurante_id del usuario autenticado (Admin)
            $user = request()->session()->get('usuario_rut');
            $restauranteId = null;
            $rutSesion = request()->session()->get('usuario_rut');

            if ($user) {
                // Buscar el restaurante donde el rut_admin coincida con el rut del usuario autenticado
                $restaurante = DB::table('restaurantes')
                    ->where('rut_admin', $rutSesion)
                    ->first();

                if ($restaurante) {
                    $restauranteId = $restaurante->id;
                } else {
                    // Si el usuario no tiene un restaurante asociado, retornar error
                    return back()->withInput()->withErrors(['db' => 'El usuario no tiene un restaurante asignado como administrador.']);
                }
            } else {
                return back()->withInput()->withErrors(['db' => 'Usuario no autenticado.']);
            }

            // Extraer solo los números del RUT (sin dígito verificador y sin puntos)
            $passwordRUT = $this->extraerPasswordRUT($rutNormalizado);

            DB::table('empleados')->insert([
                'rut' => $rutNormalizado,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'fono' => $data['fono'],
                'email' => $data['email'],
                'password' => Hash::make($passwordRUT),
                'cargo' => $data['cargo'],
                'estado' => $data['estado'] ?? 'inactivo',
                'restaurante_id' => $restauranteId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // 
            // INICIO ENVÍO DE CORREO (Después de la inserción exitosa)
            // 

            $datosCorreo = [
                'nombre'   => $data['nombre'],
                'apellido' => $data['apellido'],
                'email'    => $data['email'],
            ];

            // 💡 Usamos try/catch local para que un fallo en el correo NO detenga la creación del empleado
            try {
                Mail::to($datosCorreo['email'])->send(new EmpleadoBienvenidaMail($datosCorreo));
            } catch (Exception $e) {
                // Loguear el error para revisar luego. El usuario final no verá este error.
                Log::error('Fallo el envío de bienvenida a ' . $datosCorreo['email'] . ': ' . $e->getMessage());
                // Opcional: Podrías retornar un mensaje de éxito con advertencia.
            }

            // FIN ENVÍO DE CORREO
            // ==========================================================

        } catch (Exception $e) {
            return back()->withInput()->withErrors(['db' => 'Error al crear empleado: ' . $e->getMessage()]);
        }
        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    // Función auxiliar para normalizar RUT
    private function normalizarRUT($rut)
    {
        // Remover espacios
        $rut = trim($rut);

        // Convertir a mayúsculas
        $rut = strtoupper($rut);

        // Remover puntos
        $rut = str_replace('.', '', $rut);

        // Retornar en formato: XXXXXXXX-X
        return $rut;
    }

    // Función para validar RUT chileno (dígito verificador)
    private function validarRUT($rut)
    {
        if (!is_string($rut) || empty($rut)) return false;

        // Normalizar entrada: quitar espacios y puntos, mayúsculas
        $r = strtoupper(trim($rut));
        $r = str_replace('.', '', $r);

        // Debe contener un guión separando cuerpo y dígito verificador
        if (strpos($r, '-') === false) return false;

        [$cuerpo, $dv] = explode('-', $r);

        if (!ctype_digit($cuerpo)) return false;

        $reversed = array_reverse(str_split($cuerpo));
        $factor = 2;
        $suma = 0;

        foreach ($reversed as $digit) {
            $suma += intval($digit) * $factor;
            $factor++;
            if ($factor > 7) $factor = 2;
        }

        $resto = $suma % 11;
        $calculo = 11 - $resto;

        if ($calculo == 11) {
            $dvEsperado = '0';
        } elseif ($calculo == 10) {
            $dvEsperado = 'K';
        } else {
            $dvEsperado = (string)$calculo;
        }

        return strtoupper($dv) === $dvEsperado;
    }

    /**
     * Intento básico de verificación SMTP: conecta al MX del dominio y solicita RCPT TO
     * Nota: Muchos servidores rechazan o no responden a estas comprobaciones; puede fallar por políticas del proveedor.
     */
    private function verificarEmailSMTP($email)
    {
        // Separar usuario y dominio
        if (!is_string($email) || strpos($email, '@') === false) return false;
        [$user, $domain] = explode('@', $email, 2);

        // Obtener registros MX
        $mxhosts = [];
        if (function_exists('dns_get_record')) {
            $mx = @dns_get_record($domain, DNS_MX);
            if ($mx && is_array($mx)) {
                // ordenar por prioridad
                usort($mx, function($a, $b){ return ($a['pri'] ?? 0) - ($b['pri'] ?? 0); });
                foreach ($mx as $m) {
                    if (!empty($m['target'])) $mxhosts[] = $m['target'];
                }
            }
        }

        // Si no hay MX, usar el propio dominio como fallback
        if (empty($mxhosts)) $mxhosts[] = $domain;

        $timeout = 5; // segundos

        foreach ($mxhosts as $host) {
            // Abrir conexión SMTP (puerto 25)
            $errno = 0; $errstr = '';
            $fp = @stream_socket_client("tcp://{$host}:25", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
            if (! $fp) continue;

            stream_set_timeout($fp, $timeout);

            // Leer greeting
            $res = fgets($fp, 512);

            // Enviar EHLO
            $local = 'localhost';
            fwrite($fp, "EHLO {$local}\r\n");
            $res = fgets($fp, 512);
            // consumir posibles líneas adicionales
            while ($res && substr($res,3,1) === '-') { $res = fgets($fp,512); }

            // Enviar MAIL FROM con dirección no vacía
            $from = 'postmaster@' . $domain;
            fwrite($fp, "MAIL FROM:<{$from}>\r\n");
            $res = fgets($fp, 512);

            // Enviar RCPT TO
            fwrite($fp, "RCPT TO:<{$email}>\r\n");
            $res = fgets($fp, 512);

            // Cerrar sesión
            fwrite($fp, "QUIT\r\n");
            fclose($fp);

            if (! $res) continue;

            // Aceptado si código 250 o 251
            $code = intval(substr(trim($res),0,3));
            if (in_array($code, [250, 251])) {
                return true;
            }
            // Si servidor respondió 550 (no existe) o 551/553, consideramos inválido
            if (in_array($code, [550,551,553])) {
                return false;
            }
            // Otros códigos: intentar siguiente MX
        }

        // Si no pudo confirmar aceptación, devolver false por defecto
        return false;
    }

    // Función para extraer la contraseña (números sin dígito verificador)
    private function extraerPasswordRUT($rut)
    {
        // Remover el guión y todo lo que viene después (dígito verificador)
        $partes = explode('-', $rut);

        // Retornar solo los números sin el dígito verificador
        return $partes[0];
    }
    //función que permite la actualización de datos del empleado
    public function update(Request $request, $rut)
    {
        // Normalizar RUT: remover puntos y convertir a formato estándar
        $rutNormalizado = $this->normalizarRUT($rut);

        $data = $request->validate([
            // nombres solo letras y espacios (unicode)
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            // teléfono solo dígitos
            'fono' => ['required', 'regex:/^[0-9]+$/', 'max:15'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:empleados,email,' . $rutNormalizado . ',rut'],
            'cargo' => ['required', 'string', 'exists:roles,nombre'],
            'estado' => ['sometimes', 'in:activo,inactivo'],
        ]);

        // Verificar que el dominio del correo tenga registros MX o A (existencia del dominio)
        $emailDomain = substr(strrchr($data['email'], '@'), 1);
        if ($emailDomain === false || (!checkdnsrr($emailDomain, 'MX') && !checkdnsrr($emailDomain, 'A'))) {
            return back()->withInput()->withErrors(['email' => 'Dominio del correo no válido o no existe.']);
        }

        // Intentar verificar existencia del buzón vía SMTP (RCPT TO)
        if (! $this->verificarEmailSMTP($data['email'])) {
            return back()->withInput()->withErrors(['email' => 'No se pudo verificar el buzón de correo. Email inválido o no existente.']);
        }

        try {
            DB::table('empleados')
                ->where('rut', $rutNormalizado)
                ->update([
                    'nombre' => $data['nombre'],
                    'apellido' => $data['apellido'],
                    'fecha_nacimiento' => $data['fecha_nacimiento'],
                    'fono' => $data['fono'],
                    'email' => $data['email'],
                    'cargo' => $data['cargo'],
                    'estado' => $data['estado'] ?? 'inactivo',
                    'updated_at' => now(),
                ]);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['db' => 'Error al actualizar empleado: ' . $e->getMessage()]);
        }

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    //función que permite eliminar un empleado
    public function destroy(Request $request, $rut)
    {
        // Normalizar RUT: remover puntos y convertir a formato estándar
        $rutNormalizado = $this->normalizarRUT($rut);

        try {
            $deleted = DB::table('empleados')->where('rut', $rutNormalizado)->delete();
            if (!$deleted) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Empleado no encontrado.'], 404);
                }

                return back()->withErrors(['db' => 'Empleado no encontrado.']);
            }
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar empleado: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['db' => 'Error al eliminar empleado: ' . $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Empleado eliminado correctamente.'], 200);
        }

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }

    public function reestablecerContrasena($rut)
    {
        // 1. Normalizar RUT
        $rutNormalizado = $this->normalizarRUT($rut);

        // Extraemos pass (ej. 12345678)
        $passwordRUT = $this->extraerPasswordRUT($rutNormalizado);

        // 2. Buscar empleado
        $empleado = DB::table('empleados')->where('rut', $rutNormalizado)->first();

        if (!$empleado) {
            // Respuesta JSON de error 404
            return response()->json(['message' => 'Empleado no encontrado.'], 404);
        }

        try {
            // Si existe la tabla de tokens, generamos un token y enviamos enlace
            if (Schema::hasTable('password_set_tokens')) {
                $token = bin2hex(random_bytes(32));

                try {
                    DB::table('password_set_tokens')->insert([
                        'email' => $empleado->email,
                        'type' => 'empleado',
                        'token' => $token,
                        'expires_at' => now()->addHours(24),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (Exception $e) {
                    // Si la inserción falla, continúa con el flujo antiguo
                }

                $link = route('set-password', ['token' => $token]);

                $datosCorreo = [
                    'nombre' => $empleado->nombre,
                    'apellido' => $empleado->apellido,
                    'email' => $empleado->email,
                    'link' => $link,
                ];

                Mail::to($empleado->email)->send(new MiPrimerEmail($datosCorreo));

                return response()->json([
                    'success' => true,
                    'message' => 'Se envió un correo con un enlace para establecer la contraseña.'
                ], 200);
            }

            // 3. Actualizar DB
            DB::table('empleados')
                ->where('rut', $rutNormalizado)
                ->update([
                    'password' => Hash::make($passwordRUT),
                    'updated_at' => now(),
                ]);

            // 4. Enviar Correo
            $datosCorreo = [
                'nombre'   => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'email'    => $empleado->email // o $empleado->email según tu DB
            ];

            Mail::to($empleado->email)->send(new MiPrimerEmail($datosCorreo));

            // 5. RESPUESTA EXITOSA JSON
            return response()->json([
                'success' => true,
                'message' => 'Contraseña restablecida y correo enviado.'
            ], 200);
        } catch (Exception $e) {
            // Respuesta JSON de error servidor 500
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
}
