<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Necesario para normalizar nombres

class MedicalExamController extends Controller
{
    /**
     * BANDEJA DE ENTRADA POR ESPECIALIDAD
     */
    public function index()
    {
        $user = Auth::user();

        // Normalizamos el área del médico igual que lo hicimos al guardar el estudiante
        // Ejemplo: "Odontología" -> "odontologia"
        $userArea = Str::slug($user->role->name, '_');

        // Buscamos exámenes que:
        // 1. Contengan el área del médico en el JSON de 'requested_areas'
        // 2. El estado general NO sea 'completado'
        // 3. IMPORTANTE: Que NO tengan ya un resultado registrado para esa área específica
        $pendingExams = MedicalExam::whereJsonContains('requested_areas', $userArea)
            ->where('status', '!=', 'completado')
            ->whereDoesntHave('results', function($query) use ($userArea) {
                $query->where('area', $userArea);
            })
            ->with('student')
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    /**
     * Muestra el formulario para registrar el resultado.
     */
    public function evaluate(MedicalExam $medicalExam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        $student = $medicalExam->student;

        // Verificamos que el estudiante realmente necesite este examen
        if (!collect($medicalExam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Tu especialidad no está requerida en este circuito médico.');
        }

        // Evitar doble registro (doble seguridad)
        if ($medicalExam->results()->where('area', $userArea)->exists()) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Ya has registrado un resultado para esta evaluación.');
        }

        return view('medical_exams.create', compact('student', 'medicalExam', 'userArea'));
    }

    /**
     * Guarda los resultados técnicos.
     */
    public function storeResult(Request $request, MedicalExam $medicalExam)
    {
        $validated = $request->validate([
            'results' => 'required|array',
            'notes'   => 'nullable|string',
        ]);

        $area = Str::slug(Auth::user()->role->name, '_');

        try {
            DB::transaction(function () use ($medicalExam, $area, $validated) {
                // 1. Guardamos el resultado individual
                $medicalExam->results()->create([
                    'user_id' => Auth::id(),
                    'area'    => $area,
                    'data'    => $validated['results'],
                    'notes'   => $validated['notes'] ?? null,
                ]);

                // 2. Actualización de estado a 'en_proceso'
                if ($medicalExam->status === 'pendiente') {
                    $medicalExam->update(['status' => 'en_proceso']);
                }

                // 3. Lógica de cierre automático mejorada
                $requestedAreas = collect($medicalExam->requested_areas)->sort()->values();
                $completedAreas = $medicalExam->results()->pluck('area')->sort()->values();

                // Si el conteo coincide, marcamos como completado
                // (Ya validamos en el index que no se repitan áreas)
                if ($requestedAreas->count() === $completedAreas->count()) {
                    $medicalExam->update(['status' => 'completado']);
                }
            });

            return redirect()->route('medical_exams.index')
                             ->with('success', "Evaluación de " . str_replace('_', ' ', ucfirst($area)) . " guardada correctamente.");

        } catch (\Exception $e) {
            Log::error("Error al guardar examen médico: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error técnico al guardar: ' . $e->getMessage()]);
        }
    }

    public function finish(MedicalExam $medicalExam)
    {
        $medicalExam->update(['status' => 'completado']);
        return redirect()->route('medical_exams.index')
                         ->with('success', 'El circuito médico ha sido cerrado manualmente.');
    }
}
