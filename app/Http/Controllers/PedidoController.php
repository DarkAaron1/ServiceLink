<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index( request $request)
    {
        // Use centralized actor detection (Usuario or Empleado)
        $actor = $this->getActor($request);
        if (! $actor) {
            return redirect()->route('login.empleado')->withErrors(['auth' => 'Acceso restringido. Por favor, inicia sesión como Empleado.']);
        }

        // Build a light user object for the view (compat with previous variable names)
        $usuario = $actor['model'] ?? (object) [
            'nombre' => $actor['nombre'] ?? null,
            'email' => $actor['email'] ?? null,
            'rol_id' => null,
            'estado' => null,
        ];

        $rolName = $actor['rolName'] ?? null;

        // Sección de datos Comanda-Pedidos para Vista Cocina
        // Agrupamos pedidos por comanda y transformamos para que la vista
        // espere `comandas` con `detalles` (cantidad por item, observaciones, estado)
        $comandas = \App\Models\Comanda::with(['pedidos.item', 'mesa'])->get();

        $comandas->transform(function ($comanda) {
            $detalles = $comanda->pedidos->groupBy(function ($p) {
                return ($p->item_id ?? '0') . '|' . ($p->observaciones ?? '');
            })->map(function ($group) {
                $first = $group->first();
                return (object) [
                    'item' => $first->item ?? null,
                    'cantidad' => $group->count(),
                    'observaciones' => $first->observaciones ?? null,
                    // compatibilidad con distintas columnas posibles
                    'estado' => $first->estado ?? ($first->estado_preparacion ?? 'pendiente'),
                ];
            })->values();

            // Attach detalles para que la vista funcione sin cambios
            $comanda->detalles = $detalles;
            return $comanda;
        });

        return view('cocina.index', compact('usuario', 'rolName', 'comandas'));
    }

    /**
     * Endpoint to return latest comanda info for client-side polling.
     */
    public function latestOrder()
    {
        $latest = \App\Models\Comanda::orderBy('created_at', 'desc')->select('id', 'created_at')->first();

        return response()->json([
            'latest_id' => $latest ? $latest->id : null,
            'latest_created_at' => $latest ? $latest->created_at->toIso8601String() : null,
            'count' => \App\Models\Comanda::count(),
        ]);
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
    public function show(Pedido $pedido)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        //
    }
}
