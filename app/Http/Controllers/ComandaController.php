<?php

namespace App\Http\Controllers;

use App\Models\Comanda;
use App\Models\Pedido;
use App\Models\Items_Menu;
use App\Models\Mesas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComandaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

        return view('comandas.index', compact('mesas', 'items'));
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'mesa_id' => 'required|exists:mesas,id',
            'rut_empleado' => 'required|string',
            'order_items' => 'required|json',
            'observaciones_global' => 'nullable|string',
        ]);

        $orderItems = json_decode($request->input('order_items'), true);
        if (!is_array($orderItems) || empty($orderItems)) {
            return back()->with('error', 'Los items de la comanda no son válidos.');
        }

        DB::beginTransaction();
        try {
            $comanda = new Comanda();
            $comanda->rut_empleado = $data['rut_empleado'];
            $comanda->mesa_id = $data['mesa_id'];
            $comanda->estado = 'abierta';
            $comanda->save();

            // marcar mesa como ocupada
            $mesa = Mesas::find($data['mesa_id']);
            if ($mesa) {
                $mesa->estado = 'Ocupada';
                $mesa->save();
            }

            foreach ($orderItems as $it) {
                $itemId = $it['item_id'] ?? null;
                $cantidad = isset($it['cantidad']) ? max(1, intval($it['cantidad'])) : 1;
                $valor = $it['valor_item_ATM'] ?? (Items_Menu::find($itemId)->precio ?? 0);
                $observaciones = $it['observaciones'] ?? null;

                for ($i = 0; $i < $cantidad; $i++) {
                    $pedido = new Pedido();
                    $pedido->item_id = $itemId;
                    $pedido->comanda_id = $comanda->id;
                    $pedido->observaciones = $observaciones;
                    $pedido->valor_item_ATM = $valor;
                    $pedido->estado = 'pendiente';
                    $pedido->save();
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Comanda creada.', 'comanda' => $comanda]);
            }

            return redirect()->route('comandas.index')->with('success', 'Comanda creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al crear la comanda', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al crear la comanda: ' . $e->getMessage());
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
