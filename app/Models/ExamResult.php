<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends Model
{
    use HasFactory;

    /**
     * Atributos asignables masivamente.
     * 'area' identifica la especialidad (ej: 'psicologia').
     * 'data' contendrá el formulario dinámico en formato JSON.
     * 'user_id' es el ID del médico que realizó la evaluación.
     */
    protected $fillable = [
        'medical_exam_id',
        'user_id',
        'area',
        'data',
        'notes',
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
    | Relaciones Eloquent (Lógica Profesional de SnakeDev)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Resultado pertenece a un Examen Médico maestro (Circuito).
     */
    public function medicalExam(): BelongsTo
    {
        return $this->belongsTo(MedicalExam::class);
    }

    /**
     * Relación: Un Resultado fue registrado por un Usuario (Especialista).
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Helpers de Formateo
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el nombre del área con la primera letra en mayúscula.
     * Uso: $result->formatted_area
     */
    public function getFormattedAreaAttribute(): string
    {
        return ucfirst($this->area);
    }
}
