<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalExamController extends Controller
{
    /**
     * BANDEJA DE ENTRADA POR ESPECIALIDAD (Pacientes Activos)
     */
    public function index()
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');

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
     * HISTORIAL DE PACIENTES
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');
        $roleName = Str::lower($user->role->name ?? 'invitado');

        $query = MedicalExam::query()->with('student');

        if ($roleName !== 'administrador') {
            $query->whereHas('results', function($q) use ($userArea) {
                $q->where('area', $userArea);
            });
        } else {
            $query->where('status', 'completado');
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $searchTerm . '%')
                  ->orWhere('document_number', 'like', '%' . $searchTerm . '%');
            });
        }

        $completedExams = $query->latest()->paginate(15)->withQueryString();

        return view('medical_exams.history', compact('completedExams', 'userArea'));
    }

    /**
     * Muestra el formulario para registrar el resultado.
     */
    public function evaluate(MedicalExam $medicalExam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        $student = $medicalExam->student;

        if (!collect($medicalExam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Tu especialidad no está requerida en este circuito.');
        }

        if ($medicalExam->results()->where('area', $userArea)->exists()) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Ya has registrado un resultado para esta evaluación.');
        }

        return view('medical_exams.create', compact('student', 'medicalExam', 'userArea'));
    }

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
