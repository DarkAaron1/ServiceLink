<?php

namespace App\Http\Controllers;

use App\Models\Detalle_Venta;
use App\Models\Comanda;
use App\Models\Pedido;
use Illuminate\Http\Request;

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
        $comanda = Comanda::where('mesa_id', $mesaId)
            ->with(['pedidos.item', 'mesa'])
            ->first();

        $comanda->total = $comanda->pedidos->sum(function ($pedido) {
            return ($pedido->item ? $pedido->item->precio : 0) * $pedido->cantidad;
        });

        // Asegúrate de que las relaciones existan antes de acceder a ellas en la vista.
        return view('comandas.detalle_venta', compact('comanda',));
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