<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use Notifiable;

    // Tabla y clave primaria (string)
    protected $table = 'usuarios';
    protected $primaryKey = 'rut';
    public $incrementing = false;
    protected $keyType = 'string';

    // Campos que pueden rellenarse con create()
    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'password',
        'fecha_nacimiento',
        'rol_id',
        'estado',
    ];

    // Ocultar password y token en arrays/JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'email_verified_at' => 'datetime',
    ];

    // Mutator: si se asigna password lo hasheamos solo si no parece ya hasheado
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        // Bcrypt hashes suelen tener 60 caracteres; si ya tiene longitud compatible, asumimos que es hash
        if (is_string($value) && strlen($value) === 60 && (strpos($value, '$2y$') === 0 || strpos($value, '$2a$') === 0 || strpos($value, '$2b$') === 0)) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }

    // Relación con rol (asumiendo modelo Role existe)
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
