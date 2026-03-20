<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends Model
{
    use HasFactory;

    /**
     * Atributos asignables masivamente.
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
     * Laravel convertirá automáticamente el JSON en un array de PHP.
     */
    protected $casts = [
        'data' => 'array',
        'medical_exam_id' => 'integer',
        'user_id' => 'integer',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    /**
     * Un Resultado pertenece al circuito maestro.
     */
    public function medicalExam(): BelongsTo
    {
        return $this->belongsTo(MedicalExam::class);
    }

    /**
     * Un Resultado fue registrado por un Especialista (User).
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators (Modern Syntax Laravel 9+)
    |-------------------------------------------------------------------------- */

    /**
     * Formatea el nombre del área (ej: 'psicologia' -> 'Psicologia').
     */
    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value), // Asegura consistencia al guardar
        );
    }

    /**
     * Helper para obtener el IMC directamente si es área de medicina.
     * Uso en Blade: $result->imc
     */
    protected function imc(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['imc'] ?? 'N/A',
        );
    }

    /* |--------------------------------------------------------------------------
    | Métodos de Utilidad
    |-------------------------------------------------------------------------- */

    /**
     * Verifica si este resultado pertenece a un área específica.
     */
    public function isArea(string $areaName): bool
    {
        return strtolower($this->area) === strtolower($areaName);
    }
}