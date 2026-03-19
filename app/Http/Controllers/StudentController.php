<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\MedicalExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $validated = $request->validate([
            // Datos Estudiante
            'document_type'   => 'required|in:TI,CC,RC',
            'document_number' => 'required|string|unique:students,document_number|max:20',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'age'             => 'required|integer|min:3|max:20',
            'gender'          => 'required|string',
            'previous_school' => 'required|string|max:255',
            'grade'           => 'required|string|max:50',

            // Datos Acudiente (8 Campos)
            'guardian_name'         => 'required|string|max:100',
            'guardian_lastname'     => 'required|string|max:100',
            'guardian_document'     => 'required|string|max:20',
            'guardian_age'          => 'required|integer',
            'guardian_phone'        => 'required|string|max:20',
            'guardian_address'      => 'required|string|max:255',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_email'        => 'required|email|max:100',

            // Circuito
            'requested_areas' => 'required|array|min:1',
        ], [
            'document_number.unique' => 'Error: Este número de documento ya está matriculado.',
            'requested_areas.required' => 'Debe seleccionar al menos un área para el circuito médico.',
        ]);

        try {
            DB::beginTransaction();

            $student = Student::create($validated);

            $normalizedAreas = collect($validated['requested_areas'])->map(function($area) {
                return Str::slug($area, '_');
            })->toArray();

            MedicalExam::create([
                'student_id'      => $student->id,
                'user_id'         => Auth::id(),
                'requested_areas' => $normalizedAreas,
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

    /**
     * Muestra la ficha técnica del estudiante (Botón Ver).
     */
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * Muestra el formulario para editar (Botón Editar).
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Actualiza la información en la base de datos (Estudiante + 8 campos Acudiente).
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            // Validación Estudiante
            'document_type'   => 'required|in:TI,CC,RC',
            'document_number' => 'required|string|max:20|unique:students,document_number,' . $student->id,
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'age'             => 'required|integer',
            'gender'          => 'required|string',
            'previous_school' => 'required|string|max:255',
            'grade'           => 'required|string',

            // Validación Acudiente (8 Campos)
            'guardian_name'         => 'required|string|max:100',
            'guardian_lastname'     => 'required|string|max:100',
            'guardian_document'     => 'required|string|max:20',
            'guardian_age'          => 'required|integer',
            'guardian_phone'        => 'required|string|max:20',
            'guardian_address'      => 'required|string|max:255',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_email'        => 'required|email|max:100',
        ]);

        // Se actualizan todos los campos validados de una vez
        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'La ficha de ' . $student->full_name . ' ha sido actualizada correctamente.');
    }

    /**
     * Elimina el registro del estudiante.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Registro eliminado del sistema.');
    }
}
