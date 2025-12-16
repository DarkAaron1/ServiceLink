<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class UsuarioController extends Controller
{
    public function ver(){
        return view('Demo.register');
    }
    // Mostrar formulario de registro
    public function create()
    {
        // Traer roles para el select (si existen)
        $roles = DB::table('roles')->select('id', 'nombre')->get();

        return view('Demo.register', compact('roles'));
    }

    // Almacenar nuevo usuario
    public function store(Request $request)
    {
        $data = $request->validate([
            'rut' => ['required', 'string', 'max:50'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:usuarios,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'fecha_nacimiento' => ['required', 'date'],
            'estado' => ['sometimes', 'in:activo,inactivo'],
        ]);

        // Normalizar y validar RUT (formato chileno)
        $rutNormalizado = $this->normalizarRUT($data['rut']);
        if (! $this->validarRUT($rutNormalizado)) {
            return back()->withInput()->withErrors(['rut' => 'RUT inválido.']);
        }

        // Comprobar unicidad sobre RUT normalizado
        $exists = DB::table('usuarios')->where('rut', $rutNormalizado)->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['rut' => 'El RUT ya está registrado']);
        }

        // Verificar existencia del buzón (opcional, puede fallar por políticas del servidor remoto)
        try {
            if (! $this->verificarEmailSMTP($data['email'])) {
                return back()->withInput()->withErrors(['email' => 'El correo es inválido o no existe.']);
            }
        } catch (\Exception $e) {
            // Si falla la verificación por cualquier razón, rechazamos para evitar guardar correos obviamente inválidos
            return back()->withInput()->withErrors(['email' => 'No se pudo verificar el correo proporcionado.']);
        }

        try {
            // Crear usuario (el mutator del modelo hashea el password si corresponde)
            $usuario = Usuario::create([
                'rut' => $rutNormalizado,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'email' => $data['email'],
                'password' => $data['password'], // mutator se encargará de hashear si procede
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'rol_id' => '1',
                'estado' => $data['estado'] ?? 'inactivo',
            ]);
        } catch (Exception $e) {
            // Log opcional: \Log::error($e);
            return back()->withInput()->withErrors(['db' => 'Ocurrió un error al crear la cuenta. ' . $e->getMessage()]);
        }

        return redirect()->route('login')->with('success', 'Cuenta creada correctamente. Verifica tu correo si aplica.');
    }

    // Normalizar RUT: remover puntos y convertir a formato XXXXXXXX-X
    private function normalizarRUT($rut)
    {
        $rut = trim($rut);
        $rut = strtoupper($rut);
        $rut = str_replace('.', '', $rut);
        return $rut;
    }

    // Validar RUT chileno (dígito verificador)
    private function validarRUT($rut)
    {
        if (!is_string($rut) || empty($rut)) return false;

        $r = strtoupper(trim($rut));
        $r = str_replace('.', '', $r);

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

    
}
