<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    protected $table = 'restaurantes';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'fecha_creacion',
        'rut_admin',
    ];

    // Relación con usuario administrador
    public function admin()
    {
        return $this->belongsTo(Usuario::class, 'rut_admin', 'rut');
    }
}
