<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Comanda;
use App\Models\Items_Menu;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'comanda_id',
        'observaciones',
        'hora_creacion',
        'estado',
        'valor_item_ATM',
    ];

    public function comanda()
    {
        return $this->belongsTo(Comanda::class);
    }

    public function item()
    {
        return $this->belongsTo(Items_Menu::class, 'item_id');
    }
}
