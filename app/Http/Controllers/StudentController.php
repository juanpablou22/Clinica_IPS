<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\MedicalExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Importante para normalizar strings

class StudentController extends Controller
{
    /**
     * Lista de estudiantes matriculados.
     */
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Muestra el formulario de matrícula.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Guarda el estudiante y genera automáticamente el circuito médico.
     */
    public function store(Request $request)
    {
        // 1. Validaciones
        $validated = $request->validate([
            'document_type'   => 'required|in:TI,CC,RC',
            'document_number' => 'required|string|unique:students,document_number|max:20',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'grade'           => 'required|string|max:50',
            'requested_areas' => 'required|array|min:1',
        ], [
            'document_number.unique' => 'Error: Este número de documento ya está matriculado.',
            'requested_areas.required' => 'Debe seleccionar al menos un área para el circuito médico.',
        ]);

        try {
            DB::beginTransaction();

            // 2. Creación del Estudiante
            $student = Student::create([
                'document_type'   => $validated['document_type'],
                'document_number' => $validated['document_number'],
                'first_name'      => $validated['first_name'],
                'last_name'       => $validated['last_name'],
                'grade'           => $validated['grade'],
            ]);

            // MEJORA PROFESIONAL: Normalizamos las áreas para que coincidan con los Roles
            // Esto convierte "Medicina General" en "medicina_general" automáticamente
            $normalizedAreas = collect($validated['requested_areas'])->map(function($area) {
                return Str::slug($area, '_');
            })->toArray();

            // 3. Creación automática del Examen Médico (Circuito)
            MedicalExam::create([
                'student_id'      => $student->id,
                'user_id'         => Auth::id(), // Admisión/Admin que registra
                'requested_areas' => $normalizedAreas, // Se guarda como JSON limpio
                'status'          => 'pendiente',
            ]);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Estudiante matriculado y circuito médico iniciado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en matrícula/circuito: " . $e->getMessage());

            return back()->withInput()->withErrors(['error' => 'No se pudo completar el registro: ' . $e->getMessage()]);
        }
    }

    // ... (Show, Edit, Update y Destroy se mantienen igual)
}
