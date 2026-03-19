<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login'); // INICIO DE LOGIN 
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // --- Rutas de Perfil (Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Módulo de Estudiantes (SnakeDev)
    |--------------------------------------------------------------------------
    */
    Route::resource('students', StudentController::class);

    /*
    |--------------------------------------------------------------------------
    | Módulo de Circuito Médico (SnakeDev)
    |--------------------------------------------------------------------------
    */

    // 1. Bandeja de Entrada: Lista de pacientes según el ROL del médico
    Route::get('/medical-exams', [MedicalExamController::class, 'index'])
        ->name('medical_exams.index');

    // 2. Formulario de Evaluación: Acceso mediante el ID del Examen Médico
    // Nota: Cambiamos {student} por {exam} para que coincida con el Controller
    Route::get('/medical-exams/{exam}/create', [MedicalExamController::class, 'create'])
        ->name('medical_exams.create');

    // 3. Guardado de Resultados: Procesa el JSON dinámico
    Route::post('/medical-exams/{exam}/store', [MedicalExamController::class, 'storeResult'])
        ->name('medical_exams.store_result');

    // 4. Cierre Manual del Circuito (Finalizar proceso global)
    Route::patch('/medical-exams/{medicalExam}/finish', [MedicalExamController::class, 'finish'])
        ->name('medical_exams.finish');

});

require __DIR__.'/auth.php';
