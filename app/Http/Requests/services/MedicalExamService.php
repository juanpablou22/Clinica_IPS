<?php

namespace App\Services;

use App\Models\MedicalExam;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MedicalExamService
{
    /**
     * Guarda el resultado de un área y verifica si el circuito debe cerrarse.
     */
    public function saveAreaResult(MedicalExam $medicalExam, array $requestData, string $area)
    {
        return DB::transaction(function () use ($medicalExam, $area, $requestData) {
            
            // 1. Preparar datos según el área
            if ($area === 'odontologia') {
                $finalData = [
                    'odontograma' => $requestData['results'] ?? [],
                    'habitos'     => $requestData['habitos'] ?? [],
                    'odontograma_imagen' => $requestData['odontograma_imagen'] ?? null
                ];
            } else {
                // Filtramos campos innecesarios para otras áreas
                $finalData = collect($requestData)->except(['_token', 'notes', '_method'])->toArray();
            }

            // 2. Crear o actualizar el resultado en la tabla relacionada
            $medicalExam->results()->updateOrCreate(
                ['area' => $area],
                [
                    'user_id' => Auth::id(),
                    'data'    => $finalData,
                    'notes'   => $requestData['notes'] ?? 'Evaluación clínica realizada.',
                ]
            );

            // 3. Actualizar estado del circuito si estaba pendiente
            if ($medicalExam->status === 'pendiente') {
                $medicalExam->update(['status' => 'en_proceso']);
            }

            // 4. Lógica de Cierre Automático
            // Convertimos a colección para contar de forma segura
            $requestedAreas = collect($medicalExam->requested_areas);
            $requestedCount = $requestedAreas->count();
            $completedCount = $medicalExam->results()->count();

            if ($requestedCount > 0 && $requestedCount === $completedCount) {
                $medicalExam->update(['status' => 'completado']);
            }

            return $medicalExam;
        });
    }
}