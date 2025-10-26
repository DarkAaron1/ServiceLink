<?php

namespace App\Models;

<<<<<<< HEAD
use \Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Mesas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'estado',
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
=======
use Illuminate\Database\Eloquent\Model;

class Mesas extends Model
{
    //
>>>>>>> c66f546 (Creación MCR Sistema (actualizado hasta items_menu))
}
