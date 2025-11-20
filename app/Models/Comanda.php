<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comanda extends Model
{
    use HasFactory;

    protected $table = 'comandas';

    protected $fillable = [
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'rut_empleado',
        'mesa_id',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relación: una comanda pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleados::class, 'rut_empleado', 'rut');
    }

    // Relación: una comanda pertenece a una mesa
    public function mesa()
    {
        return $this->belongsTo(Mesas::class, 'mesa_id');
    }

    // Relación: una comanda tiene muchos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'comanda_id');
    }
}
