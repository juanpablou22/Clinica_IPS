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
     * Se integran los campos de la migración para identidad visual y cargos.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'job_title', // Cargo profesional (ej: CEO, Odontólogo)
        'ui_color',  // Color personalizado para el avatar y badges
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
    | Relaciones Eloquent - Arquitectura Snake_DEV
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un usuario pertenece a un Rol (Administrador, Admisión, etc.).
     * Crucial para la lógica del Sidebar y permisos de acceso.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación: Un usuario (médico/especialista) gestiona múltiples exámenes.
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /* |--------------------------------------------------------------------------
    | Helpers de Control de Acceso
    |--------------------------------------------------------------------------
    */

    /**
     * Verifica si el usuario tiene un rol específico para proteger rutas y botones.
     */
    public function hasRole(string $roleName): bool
    {
        // Normalizamos a minúsculas para evitar errores de digitación en la BD
        return $this->role && strtolower($this->role->name) === strtolower($roleName);
    }
}