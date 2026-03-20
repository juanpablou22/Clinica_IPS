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
            ->with('student')
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');
        $roleName = Str::lower($user->role->name ?? 'invitado');

        $query = MedicalExam::where('status', 'completado')->with(['student', 'results']);

        // Los especialistas solo ven su historial, el admin ve todo
        if ($roleName !== 'administrador') {
            $query->whereJsonContains('requested_areas', $userArea);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
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
        
        if (!collect($medical_exam->requested_areas)->contains($userArea)) {
            return redirect()->route('medical_exams.index')->with('error', 'Área no autorizada.');
        }

        // Si es medicina_general, usamos la vista de valoración (IMC y Antecedentes)
        $view = ($userArea === 'medicina_general') ? 'medical_exams.evaluate' : 'medical_exams.create';

        return view($view, [
            'medicalExam' => $medical_exam,
            'student' => $medical_exam->student,
            'userArea' => $userArea
        ]);
    }

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

                if ($medical_exam->status === 'pendiente') {
                    $medical_exam->update(['status' => 'en_proceso']);
                }

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