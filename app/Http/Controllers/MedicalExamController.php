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
    public function index()
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');

        $pendingExams = MedicalExam::whereJsonContains('requested_areas', $userArea)
            ->where('status', '!=', 'completado')
            ->whereDoesntHave('results', function($query) use ($userArea) {
                $query->where('area', $userArea);
            })
            ->with('student')<?php

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

        // Iniciamos la consulta base
        $query = MedicalExam::query()->with(['student', 'results']);

        // Los especialistas solo ven su historial, el admin ve todo lo completado
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

        $completedExams = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('medical_exams.history', compact('completedExams', 'userArea'));
    }

    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        
        if (!collect($medical_exam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Tu especialidad no está requerida en este circuito.');
        }

        // Selección de vista según el área
        $view = ($userArea === 'medicina_general') ? 'medical_exams.evaluate' : 'medical_exams.create';

        return view($view, [
            'medicalExam' => $medical_exam,
            'student' => $medical_exam->student,
            'userArea' => $userArea
        ]);
    }

    /**
     * Guarda los resultados, manejando lógica general y específica de Odontología.
     */
    public function storeResult(Request $request, MedicalExam $medical_exam)
    {
        $area = Str::slug(Auth::user()->role->name, '_');

        try {
            DB::transaction(function () use ($medical_exam, $area, $request) {
                
                // Lógica de datos según el área
                if ($area === 'odontologia') {
                    $request->validate([
                        'results' => 'required|array',
                        'odontograma_imagen' => 'required|string'
                    ]);

                    $finalData = [
                        'odontograma' => $request->results,
                        'habitos'     => $request->habitos ?? [],
                        'odontograma_imagen' => $request->odontograma_imagen
                    ];
                } else {
                    // Para Medicina General u otras áreas
                    $finalData = $request->except(['_token', 'notes']);
                }

                // Guardar o actualizar resultado
                $medical_exam->results()->updateOrCreate(
                    ['area' => $area],
                    [
                        'user_id' => Auth::id(),
                        'data'    => $finalData,
                        'notes'   => $request->notes ?? 'Evaluación clínica realizada.',
                    ]
                );

                // Actualizar estado del examen
                if ($medical_exam->status === 'pendiente') {
                    $medical_exam->update(['status' => 'en_proceso']);
                }

                // Cierre Automático si todas las áreas requeridas terminaron
                $requestedCount = collect($medical_exam->requested_areas)->count();
                $completedCount = $medical_exam->results()->count();

                if ($requestedCount === $completedCount) {
                    $medical_exam->update(['status' => 'completado']);
                }
            });

            return redirect()->route('medical_exams.index')->with('success', "Evaluación guardada correctamente.");

        } catch (\Exception $e) {
            Log::error("Error en storeResult: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'No se pudieron guardar los datos: ' . $e->getMessage()]);
        }
    }

    public function generateReport(MedicalExam $medical_exam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        
        // Buscamos el resultado del área que solicita el reporte
        $result = $medical_exam->results()->where('area', $userArea)->first();

        if (!$result) {
            return back()->with('error', 'No hay datos suficientes para generar el reporte de esta área.');
        }

        // Determinar qué vista de PDF usar
        $view = ($userArea === 'odontologia') ? 'pdf.odontograma' : 'medical_exams.pdf_report';

        $pdf = Pdf::loadView($view, [
            'exam'    => $medical_exam, // Compatible con ambos nombres
            'student' => $medical_exam->student,
            'data'    => $result->data,
            'habitos' => $result->data['habitos'] ?? [],
            'odontograma_img' => $result->data['odontograma_imagen'] ?? null,
            'notes'   => $result->notes,
            'date'    => $result->created_at,
            'doctor'  => $result->specialist
        ])->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true 
        ]);

        return $pdf->stream('REPORTE_'.$userArea.'_'.$medical_exam->student->document_number.'.pdf');
    }

    public function finish(MedicalExam $medical_exam)
    {
        $medical_exam->update(['status' => 'completado']);
        return redirect()->route('medical_exams.history')
                         ->with('success', 'Circuito cerrado manualmente.');
    }
}
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

<<<<<<< HEAD
=======
    /**
     * HISTORIAL DE PACIENTES
     */
>>>>>>> origin/main
    public function history(Request $request)
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');
        $roleName = Str::lower($user->role->name ?? 'invitado');

<<<<<<< HEAD
        $query = MedicalExam::where('status', 'completado')->with(['student', 'results']);
=======
        $query = MedicalExam::query()->with('student');
>>>>>>> origin/main

        // Los especialistas solo ven su historial, el admin ve todo
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

        $completedExams = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('medical_exams.history', compact('completedExams', 'userArea'));
    }

    /**
     * IMPORTANTE: El parámetro debe llamarse $medical_exam para coincidir 
     * con la ruta definida en web.php: /medical-exams/{medical_exam}/evaluate
     */
    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
<<<<<<< HEAD
        
        if (!collect($medical_exam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')->with('error', 'Área no autorizada.');
=======
        $student = $medicalExam->student;

        if (!collect($medicalExam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')
                             ->with('error', 'Tu especialidad no está requerida en este circuito.');
>>>>>>> origin/main
        }

        // Si es medicina_general, usamos la vista de valoración (IMC y Antecedentes)
        $view = ($userArea === 'medicina_general') ? 'medical_exams.evaluate' : 'medical_exams.create';

        return view($view, [
            'medicalExam' => $medical_exam,
            'student' => $medical_exam->student,
            'userArea' => $userArea
        ]);
    }

<<<<<<< HEAD
    public function storeResult(Request $request, MedicalExam $medical_exam)
    {
        $area = Str::slug(Auth::user()->role->name, '_');

        try {
            DB::transaction(function () use ($medical_exam, $area, $request) {
                // Sincronizamos o creamos el resultado del área actual
                $medical_exam->results()->updateOrCreate(
                    ['area' => $area],
                    [
                        'user_id' => Auth::id(),
                        'data'    => $request->except(['_token', 'notes']),
                        'notes'   => $request->notes ?? 'Evaluación clínica realizada.',
                    ]
                );
=======
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
            'odontograma_imagen' => 'required|string' // La captura Base64 es obligatoria
        ]);

        $area = Str::slug(Auth::user()->role->name, '_');

        try {
            DB::transaction(function () use ($medicalExam, $area, $validated) {
                // 2. Guardamos la imagen Base64 dentro del JSON 'data'
                $finalData = [
                    'odontograma' => $validated['results'],
                    'habitos'     => $validated['habitos'] ?? [],
                    'odontograma_imagen' => $validated['odontograma_imagen']
                ];

                $medicalExam->results()->create([
                    'user_id' => Auth::id(),
                    'area'    => $area,
                    'data'    => $finalData,
                    'notes'   => $validated['notes'] ?? null,
                ]);
>>>>>>> origin/main

                if ($medical_exam->status === 'pendiente') {
                    $medical_exam->update(['status' => 'en_proceso']);
                }

<<<<<<< HEAD
                // Lógica de Cierre Automático del Circuito
                $requestedCount = collect($medical_exam->requested_areas)->count();
                $completedCount = $medical_exam->results()->count();

                if ($requestedCount === $completedCount) {
                    $medical_exam->update(['status' => 'completado']);
                }
            });

            return redirect()->route('medical_exams.index')->with('success', "Evaluación guardada correctamente.");

        } catch (\Exception $e) {
            Log::error("Error en storeResult: " . $e->getMessage());
            return back()->withErrors(['error' => 'No se pudieron guardar los datos del examen.']);
        }
    }

    public function generateReport(MedicalExam $medical_exam)
    {
        // Buscamos específicamente el resultado de medicina para el PDF
        $result = $medical_exam->results()->where('area', 'medicina_general')->first();

        if (!$result) {
            return back()->with('error', 'No hay datos médicos suficientes para generar el reporte.');
        }

        // Asegúrate de que la vista exista en: resources/views/medical_exams/pdf_report.blade.php
        $pdf = Pdf::loadView('medical_exams.pdf_report', [
            'exam'    => $medical_exam,
            'student' => $medical_exam->student,
            'data'    => $result->data,
            'notes'   => $result->notes,
            'doctor'  => $result->specialist // Relación 'specialist' en el modelo ExamResult
        ]);

        return $pdf->stream('VALORACION_'.$medical_exam->student->document_number.'.pdf');
    }
}
=======
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

        // Extraemos la imagen (string Base64) que guardamos en storeResult
        $imageData = $odontologiaResult->data['odontograma_imagen'] ?? null;

        $pdf = Pdf::loadView('pdf.odontograma', [
            'student' => $medicalExam->student,
            'habitos' => $odontologiaResult->data['habitos'] ?? [],
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
>>>>>>> origin/main
