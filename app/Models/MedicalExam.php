<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalExam extends Model
{
    /**
     * Atributos asignables masivamente.
     * Estos campos permiten crear el registro del examen vinculando al estudiante y al médico.
     */
    protected $fillable = [
        'student_id',
        'user_id',
        'status', // Ej: 'pendiente', 'en_proceso', 'completado'
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica de Negocio)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Examen pertenece a un Estudiante.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relación: Un Examen es realizado/supervisado por un Usuario (Médico/Personal).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un Examen tiene muchos Resultados (uno por cada área médica).
     * Esto permite que Odontología, Optometría, etc., guarden sus hojas aquí.
     */
    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
