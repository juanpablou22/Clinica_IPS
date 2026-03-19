<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    /**
     * Atributos asignables masivamente.
     * Actualizado para el sistema escolar: Grado y Tipo de Documento.
     */
    protected $fillable = [
        'document_type',   // TI, CC, RC
        'document_number',
        'first_name',
        'last_name',
        'grade',           // Grado escolar
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica Profesional de SnakeDev)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Estudiante tiene muchos Exámenes Médicos.
     * Vital para la trazabilidad histórica de la clínica escolar.
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /**
     * Relación: Obtiene el examen médico actual que está en proceso.
     * Útil para acceder rápidamente desde la vista del médico.
     */
    public function currentExam(): HasOne
    {
        return $this->hasOne(MedicalExam::class)->where('status', '!=', 'completado')->latestOfMany();
    }

    /* |--------------------------------------------------------------------------
    | Helpers Profesionales (Accessors)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtiene el nombre completo del estudiante.
     * Uso en Blade: {{ $student->full_name }}
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Retorna el documento con su tipo para reportes.
     * Ejemplo: "TI - 1005974974"
     */
    public function getFullDocumentAttribute(): string
    {
        return "{$this->document_type} - {$this->document_number}";
    }

    /**
     * Retorna el grado formateado.
     * Uso: {{ $student->grade_label }}
     */
    public function getGradeLabelAttribute(): string
    {
        return "Grado: {$this->grade}";
    }
}
