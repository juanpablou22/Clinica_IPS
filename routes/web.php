<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirección inicial
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo de Estudiantes
    Route::resource('students', StudentController::class);

    // Módulo de Circuito Médico (CORREGIDO)
    // Usamos el nombre 'medical_exam' para que Laravel inyecte el modelo correctamente
    Route::resource('medical-exams', MedicalExamController::class)->parameters([
        'medical-exams' => 'medical_exam'
    ])->names([
        'index'   => 'medical_exams.index',
        'create'  => 'medical_exams.create',
        'store'   => 'medical_exams.store',
        'show'    => 'medical_exams.show',
        'edit'    => 'medical_exams.edit',
        'update'  => 'medical_exams.update',
        'destroy' => 'medical_exams.destroy',
    ]);

    // Rutas para procesar resultados y finalizar circuito
    Route::post('/medical-exams/{medical_exam}/result', [MedicalExamController::class, 'storeResult'])
        ->name('medical_exams.store_result');

    Route::patch('/medical-exams/{medical_exam}/finish', [MedicalExamController::class, 'finish'])
        ->name('medical_exams.finish');

    // Ruta personalizada para evaluar un examen médico
    Route::get('/medical-exams/{medical_exam}/evaluate', [MedicalExamController::class, 'evaluate'])
        ->name('medical_exams.evaluate');
});

require __DIR__.'/auth.php';
