<?php

namespace App\Services; // Namespace 

use App\Models\Student;
use App\Models\MedicalExam;
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
            $normalizedAreas = collect($data['requested_areas'])->map(function($area) {
                return Str::slug($area, '_');
            })->toArray();

            // 3. Crear el Examen Médico vinculado
            $student->medicalExams()->create([
                'user_id'         => Auth::id(),
                'requested_areas' => $normalizedAreas,
                'status'          => 'pendiente',
            ]);

            return $student;
        });
    }
}
