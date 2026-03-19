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
     * Incluye ahora toda la información del Estudiante y su Acudiente.
     */
    protected $fillable = [
        // Datos del Estudiante
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'age',
        'gender',
        'previous_school',
        'grade',

        // Datos del Acudiente
        'guardian_name',
        'guardian_lastname',
        'guardian_document',
        'guardian_age',
        'guardian_phone',
        'guardian_address',
        'guardian_relationship',
        'guardian_email',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    public function currentExam(): HasOne
    {
        return $this->hasOne(MedicalExam::class)->where('status', '!=', 'completado')->latestOfMany();
    }

    /* |--------------------------------------------------------------------------
    | Helpers / Accessors (Para el Index y Show)
    |-------------------------------------------------------------------------- */

    /**
     * Nombre completo del estudiante.
     * Uso: {{ $student->full_name }}
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Nombre completo del acudiente.
     * Uso: {{ $student->guardian_full_name }}
     */
    public function getGuardianFullNameAttribute(): string
    {
        return "{$this->guardian_name} {$this->guardian_lastname}";
    }

    /**
     * Documento formateado: "TI - 1005974974"
     */
    public function getFullDocumentAttribute(): string
    {
        return "{$this->document_type} - {$this->document_number}";
    }
}
