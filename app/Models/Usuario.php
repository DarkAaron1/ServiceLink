<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    // La clave primaria de la tabla es 'rut' 
    protected $primaryKey = 'rut';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'password',
        'fecha_nacimiento',
        'fecha_creacion',
        'estado',
        'rol_id'
    ];

    public $timestamps = true;

    
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }
}
