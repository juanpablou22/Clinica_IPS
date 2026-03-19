<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\DashboardController; // Traemos el nuevo controlador del panel
use Illuminate\Support\Facades\Route;

// Si alguien entra a la raíz, mandarlo a que se loguee
Route::get('/', function () {
    return redirect()->route('login');
});

// Esta es la ruta principal del panel. 
// Ahora ya no es una función vacía, sino que llama al DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rutas de perfil y módulos de Snake_DEV (Estudiantes y Exámenes)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::resource('students', StudentController::class);
    
    // Rutas del circuito médico
    Route::get('/medical-exams', [MedicalExamController::class, 'index'])->name('medical_exams.index');
});

require __DIR__.'/auth.php';