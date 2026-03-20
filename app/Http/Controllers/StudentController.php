<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    protected $studentService;

    /**
     * Inyectamos el Servicio para manejar la lógica de negocio.
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Lista de estudiantes con paginación.
     */
    public function index()
    {
        // latest() ordena por fecha de creación descendente
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Almacena el estudiante y su circuito médico usando el Servicio.
     * El StoreStudentRequest valida los campos antes de ejecutar este método.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            // Delegamos la creación del estudiante y el MedicalExam al servicio
            $this->studentService->registerWithMedicalCircuit($request->validated());

            return redirect()->route('students.index')
                ->with('success', 'Estudiante matriculado y circuito iniciado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error en matrícula de estudiante: " . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'No se pudo completar el registro: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra la ficha técnica e historial clínico.
     */
    public function show(Student $student)
    {
        // Cargamos las relaciones para evitar el problema de consultas N+1
        $student->load('medicalExams.results');
        return view('students.show', compact('student'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Actualiza la información básica del estudiante.
     */
    public function update(StoreStudentRequest $request, Student $student)
    {
        try {
            $student->update($request->validated());

            return redirect()->route('students.index')
                ->with('success', "La ficha de {$student->first_name} ha sido actualizada.");
        } catch (\Exception $e) {
            Log::error("Error al actualizar estudiante ID {$student->id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar los datos.']);
        }
    }

    /**
     * Buscador dinámico (para usar con fetch/AJAX en la vista).
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        $students = Student::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('document_number', 'LIKE', "%{$query}%")
            ->limit(8)
            ->get();

        return response()->json($students);
    }

    /**
     * Elimina el registro del estudiante (Soft Deletes recomendados en el modelo).
     */
    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')
                ->with('success', 'Registro eliminado del sistema.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar estudiante: " . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el registro.');
        }
    }
}