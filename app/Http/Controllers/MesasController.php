<?php

namespace App\Http\Controllers;

use App\Models\Mesas;
use App\Models\Restaurante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;


class MesasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        // Si el admin tiene contexto de restaurante, listar solo sus mesas
        $restauranteId = null;
        $rutSesion = request()->session()->get('usuario_rut');
        if ($rutSesion) {
            $rest = Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        if ($restauranteId) {
            $mesas = Mesas::with('restaurante')->where('restaurante_id', $restauranteId)->get();
        } else {
            $mesas = Mesas::with('restaurante')->get();
        }

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

        return view('mesas.index', compact('mesas'),compact('usuario', 'rolName'));
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada',
            'detalle_reserva' => 'nullable|string|max:255|required_if:estado,Reservada',
        ]);

        try {
            $mesa = new Mesas();
            $mesa->nombre = $data['nombre'];
            $mesa->estado = $data['estado'];
            // Guardar detalle_reserva si aplica
            $mesa->detalle_reserva = $data['detalle_reserva'] ?? null;
            // Asignar restaurante según contexto (admin autenticado) o petición
            $restauranteId = null;
            $rutSesion = request()->session()->get('usuario_rut');
            if ($rutSesion) {
                $rest = Restaurante::where('rut_admin', $rutSesion)->first();
                if ($rest) $restauranteId = $rest->id;
            } elseif (Auth::user() && isset(Auth::user()->rut)) {
                $rest = Restaurante::where('rut_admin', Auth::user()->rut)->first();
                if ($rest) $restauranteId = $rest->id;
            }

            if (! $restauranteId && $request->filled('restaurante_id')) {
                $restauranteId = $request->input('restaurante_id');
            }

            if (! $restauranteId) {
                throw new \Exception('No se pudo determinar el restaurante para la mesa.');
            }

            $mesa->restaurante_id = $restauranteId;
            $mesa->save();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mesa creada exitosamente',
                    'mesa' => $mesa
                ]);
            }

            return redirect()->route('mesas.index')->with('success', 'Mesa creada exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la mesa',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al crear la mesa: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Mesas $mesas)
    {
        return response()->json(Mesas::findOrFail($mesas->id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mesas $mesas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'estado' => 'required|string|in:Disponible,Ocupada,Reservada',
            'detalle_reserva' => 'nullable|string|max:255|required_if:estado,Reservada',
        ]);

        try {
            $mesa = Mesas::findOrFail($id);
            $mesa->nombre = $data['nombre'];
            $mesa->estado = $data['estado'];
            // Guardar o limpiar detalle_reserva según el estado
            if (isset($data['detalle_reserva']) && $data['estado'] === 'Reservada') {
                $mesa->detalle_reserva = $data['detalle_reserva'];
            } else {
                $mesa->detalle_reserva = null;
            }
            $mesa->save();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Mesa actualizada correctamente.', 'mesa' => $mesa]);
            }

            return redirect()->route('mesas.index')->with('success', 'Mesa actualizada correctamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la mesa',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al actualizar la mesa: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        // Buscar la mesa por su ID
        $mesa = Mesas::find($id);

        // Verificar si existe
        if (!$mesa) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'La mesa no fue encontrada.'], 404);
            }
            return redirect()->route('mesas.index')->with('error', 'La mesa no fue encontrada.');
        }

        // Eliminar la mesa
        try {
            $mesa->delete();
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar la mesa', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al eliminar la mesa: ' . $e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mesa eliminada correctamente.']);
        }

        return redirect()->route('mesas.index');
    }
}
