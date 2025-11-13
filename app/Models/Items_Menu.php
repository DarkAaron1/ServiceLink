<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items_Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'disponible',
        'items_categoria_id',
        'items_restaurante_id',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'precio' => 'decimal:2'
    ];

    public function categoria()
    {
        return $this->belongsTo(Items_Categoria::class, 'items_categoria_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'items_restaurante_id');
    }

}
