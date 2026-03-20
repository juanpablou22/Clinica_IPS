<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalExamController extends Controller
{
    // ... (Mantén los métodos index, history, evaluate que limpiamos antes)

    public function storeResult(Request $request, MedicalExam $medical_exam)
    {
        $area = Str::slug(Auth::user()->role->name, '_');

        return DB::transaction(function () use ($medical_exam, $area, $request) {
            // Lógica para Odontología o General
            $data = ($area === 'odontologia') 
                ? [
                    'odontograma' => $request->results,
                    'habitos' => $request->habitos ?? [],
                    'odontograma_imagen' => $request->odontograma_imagen
                  ]
                : $request->except(['_token', 'notes']);

            $medical_exam->results()->updateOrCreate(
                ['area' => $area],
                [
                    'user_id' => Auth::id(),
                    'data'    => $data,
                    'notes'   => $request->notes ?? 'Evaluación realizada.',
                ]
            );

            // Cierre automático
            if ($medical_exam->results()->count() === collect($medical_exam->requested_areas)->count()) {
                $medical_exam->update(['status' => 'completado']);
            }

            return redirect()->route('medical_exams.index')->with('success', 'Guardado.');
        });
    /**
     * Permite re-evaluar un examen aunque ya exista resultado previo para el área.
     */
    public function revaluate(MedicalExam $medicalExam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        $student = $medicalExam->student;

        if (!collect($medicalExam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.history')
                             ->with('error', 'Tu especialidad no está requerida en este circuito.');
        }

        return view('medical_exams.create', compact('student', 'medicalExam', 'userArea'));
    }

    /**
     * Guarda los resultados técnicos, imagen del odontograma y hábitos.
     */
    public function storeResult(Request $request, MedicalExam $medicalExam)
    {
        // 1. Validamos incluyendo el campo de la imagen capturada por JS
        $validated = $request->validate([
            'results' => 'required|array',
            'habitos' => 'nullable|array',
            'notes'   => 'nullable|string',
            'odontograma_imagen' => 'required|string', // La captura Base64 es obligatoria
            'odontograma_json' => 'nullable|string',
        ]);

        $area = Str::slug(Auth::user()->role->name, '_');

        try {
            DB::transaction(function () use ($medicalExam, $area, $validated) {
                // 2. Normalizamos los resultados de cada cara del diente en un odonto completo por diente
                $toothFaces = $validated['results'] ?? [];
                $odontogramaTeeth = [];
                $facePriority = ['red', 'blue', 'green', 'gray', 'white'];

                foreach (['top', 'right', 'bottom', 'left', 'center'] as $face) {
                    if (!empty($toothFaces[$face]) && is_array($toothFaces[$face])) {
                        foreach ($toothFaces[$face] as $toothNumber => $color) {
                            $odontogramaTeeth[$toothNumber]['faces'][$face] = $color;
                        }
                    }
                }

                // Si el cliente trajo JSON estructurado, priorizamos esos valores
                if (!empty($validated['odontograma_json'])) {
                    $parsedOdontograma = json_decode($validated['odontograma_json'], true);
                    if (is_array($parsedOdontograma)) {
                        foreach ($parsedOdontograma as $toothNumber => $toothData) {
                            if (!empty($toothData['faces']) && is_array($toothData['faces'])) {
                                $odontogramaTeeth[$toothNumber]['faces'] = $toothData['faces'];
                            }
                        }
                    }
                }

                // Clasificamos un color general por diente según prioridad clínica
                foreach ($odontogramaTeeth as $toothNumber => $data) {
                    $colors = array_values($data['faces'] ?? []);
                    $odontogramaTeeth[$toothNumber]['status'] = 'white';

                    foreach ($facePriority as $priorityColor) {
                        if (in_array($priorityColor, $colors, true)) {
                            $odontogramaTeeth[$toothNumber]['status'] = $priorityColor;
                            break;
                        }
                    }
                }

                $finalData = [
                    'odontograma' => $odontogramaTeeth,
                    'habitos'     => $validated['habitos'] ?? [],
                    'odontograma_imagen' => $validated['odontograma_imagen']
                ];

                $result = $medicalExam->results()->where('area', $area)->first();

                if ($result) {
                    $result->update([
                        'user_id' => Auth::id(),
                        'data'    => $finalData,
                        'notes'   => $validated['notes'] ?? null,
                    ]);
                } else {
                    $medicalExam->results()->create([
                        'user_id' => Auth::id(),
                        'area'    => $area,
                        'data'    => $finalData,
                        'notes'   => $validated['notes'] ?? null,
                    ]);
                }

                if ($medicalExam->status === 'pendiente') {
                    $medicalExam->update(['status' => 'en_proceso']);
                }

                $requestedAreas = collect($medicalExam->requested_areas)->count();
                $completedAreas = $medicalExam->results()->count();

                if ($requestedAreas === $completedAreas) {
                    $medicalExam->update(['status' => 'completado']);
                }
            });

            return redirect()->route('medical_exams.history')
                             ->with('success', "Evaluación guardada y captura generada correctamente.");

        } catch (\Exception $e) {
            Log::error("Error al guardar examen: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * GENERAR REPORTE PDF (Con soporte para imágenes Base64)
     */
    public function generateReport(MedicalExam $medicalExam)
    {
        $odontologiaResult = $medicalExam->results()
            ->where('area', 'odontologia')
            ->firstOrFail();

        // Extraemos los datos (odontograma, hábitos, imagen) del JSON
        $data = $odontologiaResult->data ?? [];
        $odontogramaData = $data['odontograma'] ?? [];
        $habitos = $data['habitos'] ?? [];
        $imageData = $data['odontograma_imagen'] ?? null;

        $pdf = Pdf::loadView('pdf.odontograma', [
            'student' => $medicalExam->student,
            'odontograma' => $odontogramaData,
            'habitos' => $habitos,
            'odontograma_img' => $imageData,
            'notes'   => $odontologiaResult->notes,
            'date'    => $odontologiaResult->created_at
        ])->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true // Permite procesar la imagen capturada
        ]);

        return $pdf->stream('Reporte_Odontologico_'.$medicalExam->student->document_number.'.pdf');
    }

    public function finish(MedicalExam $medicalExam)
    {
        $medicalExam->update(['status' => 'completado']);
        return redirect()->route('medical_exams.history')
                         ->with('success', 'Circuito cerrado manualmente.');
    }
}