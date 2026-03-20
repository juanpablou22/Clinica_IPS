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
use Barryvdh\DomPDF\Facade\Pdf; // Importación necesaria para el PDF

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
     * HISTORIAL DE PACIENTES (Exámenes Completados)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $userArea = Str::slug($user->role->name, '_');
        $roleName = Str::lower($user->role->name ?? 'invitado');

        $query = MedicalExam::where('status', 'completado')->with('student');

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
                             ->with('error', 'Tu especialidad no está requerida en este circuito médico.');
        }

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
                $medicalExam->results()->create([
                    'user_id' => Auth::id(),
                    'area'    => $area,
                    'data'    => $validated['results'],
                    'notes'   => $validated['notes'] ?? null,
                ]);

                if ($medicalExam->status === 'pendiente') {
                    $medicalExam->update(['status' => 'en_proceso']);
                }

                $requestedAreas = collect($medicalExam->requested_areas)->sort()->values();
                $completedAreas = $medicalExam->results()->pluck('area')->sort()->values();

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

    /**
     * GENERAR REPORTE PDF DEL ODONTOGRAMA
     */
    public function generateReport(MedicalExam $medicalExam)
    {
        // Buscamos el resultado específico del área de odontología
        $odontologiaResult = $medicalExam->results()
            ->where('area', 'odontologia')
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.odontograma', [
            'student' => $medicalExam->student,
            'results' => $odontologiaResult->data, // Extrae el array de colores
            'notes'   => $odontologiaResult->notes,
            'date'    => $odontologiaResult->created_at
        ]);

        // Retorna el PDF para visualizar en el navegador
        return $pdf->stream('Reporte_Odontologico_'.$medicalExam->student->document_number.'.pdf');
    }

    public function finish(MedicalExam $medicalExam)
    {
        $medicalExam->update(['status' => 'completado']);
        return redirect()->route('medical_exams.index')
                         ->with('success', 'El circuito médico ha sido cerrado manualmente.');
    }
}
