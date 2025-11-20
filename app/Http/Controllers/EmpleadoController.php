<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Usuario; // agregado
use Illuminate\Support\Facades\Mail;
use App\Mail\MiPrimerEmail;
use App\Mail\EmpleadoBienvenidaMail;

class EmpleadoController extends Controller
{
    // Mostrar listado y formulario (roles/restaurantes para select en modal)
    public function index()
    {
        // Obtener empleados (simple listado)
        $empleados = DB::table('empleados')->select('rut','nombre','apellido','email','fono','fecha_nacimiento','cargo','estado','restaurante_id')->get();

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
        return view('colaboradores.index', compact('empleados','roles','usuario','rolName'));
    }

    // Almacenar nuevo empleado
    public function store(Request $request)
    {
        // Normalizar RUT: remover puntos y convertir a formato estándar
        $rutInput = $request->input('rut');
        $rutNormalizado = $this->normalizarRUT($rutInput);

        $data = $request->validate([
            'rut' => ['required','string','max:50'],
            'nombre' => ['required','string','max:100'],
            'apellido' => ['required','string','max:100'],
            'fecha_nacimiento' => ['required','date'],
            'fono' => ['required','numeric'],
            'email' => ['required','email','max:255','unique:empleados,email'],
            'cargo' => ['required','string','exists:roles,nombre'],
            'estado' => ['sometimes','in:activo,inactivo'],
        ]);

        // Validar que el RUT no exista (normalizado)
        $rutExistente = DB::table('empleados')
            ->where('rut', $rutNormalizado)
            ->first();

        if ($rutExistente) {
            return back()->withInput()->withErrors(['rut' => 'El RUT ya está registrado']);
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
            // ==========================================================
        // 📩 INICIO ENVÍO DE CORREO (Después de la inserción exitosa)
        // ==========================================================
        
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
            \Log::error('Fallo el envío de bienvenida a ' . $datosCorreo['email'] . ': ' . $e->getMessage());
            // Opcional: Podrías retornar un mensaje de éxito con advertencia.
        }

        // 📩 FIN ENVÍO DE CORREO
        // ==========================================================

    } catch (Exception $e) { 
        return back()->withInput()->withErrors(['db' => 'Error al crear empleado: '.$e->getMessage()]);
    }
        return redirect()->route('empleados.index')->with('success','Empleado creado correctamente.');
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
            'nombre' => ['required','string','max:100'],
            'apellido' => ['required','string','max:100'],
            'fecha_nacimiento' => ['required','date'],
            'fono' => ['required','numeric'],
            'email' => ['required','email','max:255','unique:empleados,email,'.$rutNormalizado.',rut'],
            'cargo' => ['required','string','exists:roles,nombre'],
            'estado' => ['sometimes','in:activo,inactivo'],
        ]);

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
            return back()->withInput()->withErrors(['db' => 'Error al actualizar empleado: '.$e->getMessage()]);
        }

        return redirect()->route('empleados.index')->with('success','Empleado actualizado correctamente.');
    }

    //función que permite eliminar un empleado
    public function destroy($rut)
    {
        // Normalizar RUT: remover puntos y convertir a formato estándar
        $rutNormalizado = $this->normalizarRUT($rut);

        try {
            DB::table('empleados')->where('rut', $rutNormalizado)->delete();
        } catch (Exception $e) {
            return back()->withErrors(['db' => 'Error al eliminar empleado: '.$e->getMessage()]);
        }

        return redirect()->route('empleados.index')->with('success','Empleado eliminado correctamente.');
    }

        public function reestablecerContrasena($rut)
    {
        // 1. Normalización (asegúrate de tener esta lógica o limpiar el rut aquí)
        // Si no tienes la función normalizarRUT, usa: str_replace(['.', '-'], '', $rut);
        $rutNormalizado = $this->normalizarRUT($rut); 
        
        // Extraemos pass (ej. 12345678)
        // Si no tienes la función, usa: substr($rutNormalizado, 0, -1);
        $passwordRUT = $this->extraerPasswordRUT($rutNormalizado); 

        // 2. Buscar empleado
        $empleado = \DB::table('empleados')->where('rut', $rutNormalizado)->first();

        if (!$empleado) {
            // Respuesta JSON de error 404
            return response()->json(['message' => 'Empleado no encontrado.'], 404);
        }

        try {
            // 3. Actualizar DB
            \DB::table('empleados')
                ->where('rut', $rutNormalizado)
                ->update([
                    'password' => \Hash::make($passwordRUT),
                    'updated_at' => now(),
                ]);

            // 4. Enviar Correo
            $datosCorreo = [
                'nombre'   => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'email'    => $empleado->email // o $empleado->email según tu DB
            ];

            \Mail::to($empleado->email)->send(new MiPrimerEmail($datosCorreo));

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