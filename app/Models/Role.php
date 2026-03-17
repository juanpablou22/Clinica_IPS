<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * Los atributos que son asignables masivamente.
     * Solo necesitamos el nombre del rol (ej: 'Odontología').
     */
    protected $fillable = [
        'name',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica Profesional)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Rol tiene muchos Usuarios.
     * Esto te permite hacer: $role->users para ver, por ejemplo, todos los Optómetras.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
