<?php

namespace App\Http\Controllers;

use App\Models\Detalle_Venta;
use App\Models\Comanda;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mesas;

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function detalleVenta($mesaId)
    {
        // Obtener todos los pedidos relacionados a las comandas de la mesa
        $pedidos = Pedido::whereHas('comanda', function ($q) use ($mesaId) {
            $q->where('mesa_id', $mesaId);
        })->with('item', 'comanda')->get();

        if ($pedidos->isEmpty()) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'No hay pedidos para esta mesa'], 404);
            }
            abort(404, 'No hay pedidos para esta mesa');
        }

        // Agrupar por item_id y calcular cantidad (conteo de filas)
        $grouped = $pedidos->groupBy('item_id')->map(function ($group) {
            $first = $group->first();
            // Preferir el valor histórico del pedido si existe, sino el precio del item
            $precioUnit = $first->valor_item_ATM ?? $first->item?->precio ?? 0;
            $cantidad = $group->count();
            $subtotal = $precioUnit * $cantidad;

            return [
                'item_id' => $first->item?->id ?? $first->item_id,
                'nombre' => $first->item?->nombre ?? 'N/A',
                'precio_unitario' => $precioUnit,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ];
        })->values();

        $total = $grouped->sum('subtotal');

        // Si la petición solicita JSON, devolver la estructura agregada
        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            // Obtener nombre de mesa y estado desde la comanda más reciente relacionada (si existe)
            $comandaModel = $pedidos->first()->comanda ?? null;
            return response()->json([
                'mesa' => $comandaModel?->mesa?->nombre ?? null,
                'estado' => $comandaModel?->estado ?? null,
                'observaciones_global' => $comandaModel?->observaciones_global ?? null,
                'grouped_pedidos' => $grouped,
                'total' => $total,
            ]);
        }

        // Para la vista tradicional (no AJAX) pasamos la colección de pedidos y agrupado
        $comanda = $pedidos->first()->comanda ?? null;
        return view('comandas.detalle_venta', compact('comanda', 'grouped', 'total'));
    }

    /**
     * Liberar la mesa y cerrar la comanda abierta
     */
    public function liberarMesa(Request $request, $mesaId)
    {
        $comanda = Comanda::where('mesa_id', $mesaId)->where('estado_cuenta', 'abierta')->first();

        if (! $comanda) {
            return response()->json(['success' => false, 'message' => 'No hay comanda abierta para esta mesa.'], 404);
        }

        DB::beginTransaction();
        try {
            // Marcar comanda como cerrada
            $comanda->estado_cuenta = 'cerrada';
            $comanda->save();

            // Actualizar estado de la mesa a Disponible
            $mesa = Mesas::find($mesaId);
            if ($mesa) {
                $mesa->estado = 'Disponible';
                $mesa->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Mesa liberada correctamente.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al liberar mesa: ' . $e->getMessage()], 500);
        }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Detalle_Venta $detalle_Venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Detalle_Venta $detalle_Venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Detalle_Venta $detalle_Venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Detalle_Venta $detalle_Venta)
    {
        //
    }
}
