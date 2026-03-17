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
     * Se agrega 'role_id' para vincular al usuario con su función en la clínica.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* |
    | Relaciones Eloquent (Lógica Profesional)
    |
    */

    /**
     * Relación: Un usuario pertenece a un Rol.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación: Un médico (User) puede haber realizado muchos exámenes.
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /*
    | Helpers de Seguridad

    */

    /**
     * Verifica si el usuario tiene un rol específico.
     * Útil para proteger rutas y apartados médicos.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role->name === $roleName;
    }
}
