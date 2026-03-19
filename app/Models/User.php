<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Atributos asignables masivamente.
     * Se incluye 'role_id' para la gestión de permisos en la Clínica IPS.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * Atributos ocultos para la serialización.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión de tipos de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica Profesional de SnakeDev)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un usuario pertenece a un Rol (Admin, Médico, Recepción).
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación: Un usuario (médico) puede haber realizado y firmado muchos exámenes.
     * Permite la trazabilidad de quién atendió a cada estudiante.
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /* |--------------------------------------------------------------------------
    | Helpers de Seguridad y Control de Acceso
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si el usuario tiene un rol específico.
     * Uso: if(auth()->user()->hasRole('médico')) { ... }
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }
}
