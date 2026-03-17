<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /**
     * Atributos asignables masivamente.
     * Estos son los campos base que definimos en la migración.
     */
    protected $fillable = [
        'document_number',
        'first_name',
        'last_name',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent (Lógica Profesional)
    |--------------------------------------------------------------------------
    */

    /**
     * Relación: Un Estudiante tiene muchos Exámenes Médicos.
     * Esto es vital para la trazabilidad histórica de la clínica.
     * Permite hacer: $student->medicalExams
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /* |--------------------------------------------------------------------------
    | Helpers Profesionales (Accesorios)
    |--------------------------------------------------------------------------
    */

    /**
     * Atributo virtual para obtener el nombre completo.
     * Facilita mostrar el nombre en las vistas sin concatenar siempre.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
