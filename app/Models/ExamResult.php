<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_exam_id',
        'user_id',
        'area',
        'data',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'medical_exam_id' => 'integer',
        'user_id' => 'integer',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function medicalExam(): BelongsTo
    {
        return $this->belongsTo(MedicalExam::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators (Corregidos para Snake_DEV)
    |-------------------------------------------------------------------------- */

    /**
     * IMPORTANTE: El nombre de la función debe ser igual al campo 'area'
     */
    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => strtolower($value),
        );
    }

    /**
     * Accessor virtual para el IMC.
     * Como no existe una columna 'imc', Laravel lo tratará como un atributo dinámico.
     */
    protected function imc(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->data['biometria']['imc'] ?? ($this->data['imc'] ?? 'N/A'),
        );
    }

    /**
     * Accessor para obtener el color del estado del IMC
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                $status = $this->data['biometria']['imc_status'] ?? ($this->data['imc_status'] ?? '');
                return match ($status) {
                    'Normal' => 'text-green-600 bg-green-50',
                    'Sobrepeso' => 'text-yellow-600 bg-yellow-50',
                    'Obesidad' => 'text-red-600 bg-red-50',
                    'Bajo Peso' => 'text-orange-600 bg-orange-50',
                    default => 'text-slate-400 bg-slate-50',
                };
            },
        );
    }

    /* |--------------------------------------------------------------------------
    | Métodos de Utilidad
    |-------------------------------------------------------------------------- */

    public function isArea(string $areaName): bool
    {
        // Usamos $this->attributes['area'] para evitar el ucfirst del accessor al comparar
        return strtolower($this->getRawOriginal('area')) === strtolower($areaName);
    }
}