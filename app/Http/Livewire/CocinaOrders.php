<?php

namespace App\Http\Livewire;

use App\Models\Pedido;
use Livewire\Component;
use App\Models\Comanda;
use App\Models\Items_Menu;


class CocinaOrders extends Component
{
    protected $listeners = [
        'refreshOrders' => '$refresh',
    ];

    public function render()
    {
        // 1. FILTRAR EN LA CONSULTA: Excluir las que están 'listo'
        $comandas = Comanda::with(['pedidos.item', 'mesa'])
            ->where('estado', '!=', 'listo') 
            ->orderBy('created_at', 'desc')
            ->get();
            
        $comandas->transform(function ($comanda) {
            $detalles = $comanda->pedidos->groupBy(function ($p) {
                return ($p->item_id ?? '0') . '|' . ($p->observaciones ?? '');
            })->map(function ($group) {
                $first = $group->first();
                return (object) [
                    'item' => $first->item ?? null,
                    'cantidad' => $group->count(),
                    'observaciones' => $first->observaciones ?? null,
                    'estado' => $first->estado ?? ($first->estado_preparacion ?? 'pendiente'),
                ];
            })->values();

            $comanda->detalles = $detalles;
            return $comanda;
        });

        return view('livewire.cocina-orders', compact('comandas'));
    }

    // Actualizar estado a 'en_preparacion'
    public function markPreparing($comandaId)
    {
        $c = Comanda::find($comandaId);
        if (! $c) return;
        $c->estado = 'en_preparacion';
        $c->save();
        $this->dispatch('refreshOrders');
    }

    // Actualizar estado a 'listo'
    public function markReady($comandaId)
    {
        $c = Comanda::find($comandaId);
        if (! $c) return;
        $c->estado = 'listo';
        $p = Pedido::find($comandaId);
        if (! $p) return;
        $p->estado = 'entregado';
        $p->save();
        $c->save();
        $this->dispatch('refreshOrders');
    }
}
