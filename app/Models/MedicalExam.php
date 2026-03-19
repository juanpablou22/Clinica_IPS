<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class MedicalExam extends Model
{
    use HasFactory;

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'student_id',
        'user_id',         // ID de la secretaria/admin que creó el circuito
        'requested_areas', // Array JSON (odontologia, optometria, etc.)
        'status',          // 'pendiente', 'en_proceso', 'completado'
        'observations',
        'result_type',
    ];

    /**
     * Conversión de tipos.
     * Crucial para que Laravel maneje el JSON de TiDB como un array de PHP.
     */
    protected $casts = [
        'requested_areas' => 'array',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Usuario (Secretaria/Admin) que inició el circuito médico.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Los resultados individuales cargados por cada médico especialista.
     */
    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    /* |--------------------------------------------------------------------------
    | Scopes (Filtros Inteligentes para la Bandeja de Pacientes)
    |--------------------------------------------------------------------------
    */

    /**
     * Filtra los exámenes según el área asignada al médico logueado.
     * Uso: MedicalExam::forArea('odontologia')->pendiente()->get();
     */
    public function scopeForArea(Builder $query, string $area): Builder
    {
        // Normalizamos a minúsculas para evitar errores de coincidencia
        return $query->whereJsonContains('requested_areas', strtolower($area));
    }

    public function scopePendiente(Builder $query): Builder
    {
        return $query->where('status', 'pendiente');
    }

    /* |--------------------------------------------------------------------------
    | Helpers de Lógica de Negocio
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === 'completado';
    }

    /**
     * Verifica si un área específica (ej. 'odontologia') ya tiene un resultado guardado.
     */
    public function isAreaCompleted(string $areaName): bool
    {
        return $this->results()->where('area', strtolower($areaName))->exists();
    }
}
