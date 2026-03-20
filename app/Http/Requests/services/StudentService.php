<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentService
{
    /**
     * Registra un estudiante y crea su circuito médico en una sola transacción.
     */
    public function registerWithMedicalCircuit(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear el Estudiante con los datos validados
            $student = Student::create($data);

            // 2. Normalizar las áreas (ej: "Medicina General" -> "medicina_general")
            // Usamos collect() asegurando que requested_areas sea un array
            $requestedAreas = $data['requested_areas'] ?? [];
            
            $normalizedAreas = collect($requestedAreas)->map(function($area) {
                return Str::slug($area, '_');
            })->toArray();

            // 3. Crear el Examen Médico vinculado (Circuito)
            $student->medicalExams()->create([
                'user_id'         => Auth::id(),
                'requested_areas' => $normalizedAreas,
                'status'          => 'pendiente',
            ]);

            return $student;
        });
    }
}
