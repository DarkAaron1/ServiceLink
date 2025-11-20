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
        'estado',
        'categoria_id',
        'restaurante_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2'
    ];

    public function categoria()
    {
        return $this->belongsTo(Items_Categoria::class, 'categoria_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }

}
