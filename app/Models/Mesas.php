<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Mesas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'estado',
        'detalle_reserva',
        'restaurante_id',
    ];

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class);
    }

    public function comandas()
    {
        return $this->hasMany(Comanda::class);
    }
}
