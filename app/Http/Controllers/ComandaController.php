<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Pedido;
use App\Models\Items_Menu;
use App\Models\Mesas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Restaurante;
use App\Events\NuevoPedidoCreado;

class ComandaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( request $request)
    {
        // Determine restaurant context similar to other controllers
        $restauranteId = null;
        $rutSesion = request()->session()->get('usuario_rut');
        if ($rutSesion) {
            $rest = \App\Models\Restaurante::where('rut_admin', $rutSesion)->first();
            if ($rest) $restauranteId = $rest->id;
        } elseif (Auth::user() && isset(Auth::user()->rut)) {
            $rest = \App\Models\Restaurante::where('rut_admin', Auth::user()->rut)->first();
            if ($rest) $restauranteId = $rest->id;
        }

        if ($restauranteId) {
            $mesas = Mesas::where('restaurante_id', $restauranteId)->get();
            $items = Items_Menu::where('restaurante_id', $restauranteId)->where('estado', 'disponible')->get();
        } else {
            $mesas = Mesas::all();
            $items = Items_Menu::where('estado', 'disponible')->get();
        }

        // Datos del actor (Usuario o Empleado)
        $actor = $this->getActor($request);
        if (! $actor) {
            return redirect()->route('login');
        }

        $usuario = $actor['model'] ?? (object) ['nombre' => $actor['nombre'], 'email' => $actor['email']];
        $rolName = $actor['rolName'] ?? null;

        // Si hay restaurante de empleado en sesión, usarlo
        if ($actor['restaurante_id']) {
            $restauranteId = $actor['restaurante_id'];
            $mesas = Mesas::where('restaurante_id', $restauranteId)->get();
            $items = Items_Menu::where('restaurante_id', $restauranteId)->where('estado', 'disponible')->get();
        }

        return view('comandas.index', compact('mesas', 'items', 'usuario', 'rolName'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
private function normalizarRut(?string $rut): ?string
    {
        if (empty($rut)) return null;
        $rut = str_replace(['.', ' '], '', $rut);
        return strtoupper($rut);
    }

    public function store(Request $request)
    {
        // 1. Depuración y Validación
        \Log::info('Inicio ComandaController@store', $request->all());

        $datosValidados = $request->validate([
            'mesa_id' => 'required|exists:mesas,id', // 'mesas' es la tabla en BD
            'order_items' => 'required|json',
            'observaciones_global' => 'nullable|string',
        ]);
        
        $orderItems = json_decode($datosValidados['order_items'], true);
        
        if (!is_array($orderItems) || empty($orderItems)) {
            return back()->with('error', 'Debe seleccionar al menos un ítem válido.');
        }

        DB::beginTransaction();
        try {
            // 2. Normalizar RUT (Solución error 1452)
            $rutSesion = $request->session()->get('empleado_rut') ?? $request->session()->get('usuario_rut');
            $rutEmpleado = $this->normalizarRut($rutSesion);
            
            if (empty($rutEmpleado)) {
                throw new \Exception('No se pudo validar el RUT del empleado en sesión.');
            }
            
            // 3. Crear Comanda
            $comanda = Comanda::create([
                'rut_empleado' => $rutEmpleado,
                'mesa_id' => $datosValidados['mesa_id'],
                'estado_cuenta' => 'abierta', 
            ]);

            // 4. Actualizar Mesa (Usando el modelo 'Mesas')
            $mesa = Mesas::findOrFail($datosValidados['mesa_id']); 
            $mesa->estado = 'Ocupada';
            $mesa->save();

            // 5. Crear Pedidos
            foreach ($orderItems as $it) {
                // Usando el modelo 'Items_Menu'
                $item = Items_Menu::findOrFail($it['item_id'] ?? null);

                $cantidad = isset($it['cantidad']) ? max(1, intval($it['cantidad'])) : 1;
                $precio = $it['valor_item_ATM'] ?? $item->precio; 
                $observaciones = $it['observaciones'] ?? null;

                for ($i = 0; $i < $cantidad; $i++) {
                    $pedido = $comanda->pedidos()->create([
                        'item_id' => $item->id,
                        'item_nombre' => $item->nombre, 
                        'valor_item_ATM' => $precio, 
                        'observaciones' => $observaciones,
                        'estado_preparacion' => 'pendiente', 
                    ]);
                    
                    // 6. Tiempo Real
                    NuevoPedidoCreado::dispatch($pedido); 
                }
            }

            DB::commit();
            \Log::info('Comanda creada ID: ' . $comanda->id);

            // Información de diagnóstico: comprobar si broadcasting está activo
            \Log::info('Broadcast driver configured: ' . config('broadcasting.default'));

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'comanda' => $comanda->load('pedidos')], 201);
            }

            return redirect()->route('comandas.index')->with('success', 'Comanda creada correctamente');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error FATAL en Store:', ['error' => $e->getMessage()]);
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comanda $comanda)
    {
        // Devolver detalles de la comanda con pedidos
        $comanda->load('pedidos.item', 'mesa');
        if (request()->wantsJson()) {
            return response()->json($comanda);
        }
        return view('comandas.show', compact('comanda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comanda $comanda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comanda $comanda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comanda $comanda)
    {
        //
    }
}