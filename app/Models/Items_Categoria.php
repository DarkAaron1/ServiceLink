<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Items_Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'restaurante_id',
    ];


    public function items(): HasMany
    {
        // Si la categoría está asociada a un restaurante, limitar los items a ese restaurante
        $relation = $this->hasMany(Items_Menu::class, 'categoria_id');
        if (! is_null($this->restaurante_id)) {
            $relation->where('restaurante_id', $this->restaurante_id);
        }
        return $relation;
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }

}
