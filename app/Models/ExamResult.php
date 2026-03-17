<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamResult extends Model
{
    /**
     * Atributos asignables masivamente.
     * 'area' identifica la especialidad (ej: 'Psicología').
     * 'data' contendrá el formulario dinámico en formato JSON.
     */
    protected $fillable = [
        'medical_exam_id',
        'area',
        'data',
    ];

    /**
     * Conversión de tipos (Casting).
     * Esto es VITAL: Laravel convertirá automáticamente el JSON de la BD
     * en un array de PHP para que lo manipules fácilmente.
     */
    protected $casts = [
        'data' => 'array',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica de Especialidad)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Resultado pertenece a un Examen Médico maestro.
     */
    public function medicalExam(): BelongsTo
    {
        return $this->belongsTo(MedicalExam::class);
    }
}
