<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Items_Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];


    public function items(): HasMany
    {
        return $this->hasMany(Items_Menu::class, 'categoria_id');
    }

}
