<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mesas;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comanda extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut_empleado',
        'mesa_id',
        'estado',
        'fecha_apertura',
        'fecha_cierre',
    ];

    public function mesa()
    {
        return $this->belongsTo(Mesas::class, 'mesa_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
    //
}
