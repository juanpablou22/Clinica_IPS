<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Snake_DEV System
|--------------------------------------------------------------------------
*/

// Redirección inicial al Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard Principal
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    /* |--- PERFIL DE USUARIO ---| */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* |--- MÓDULO DE ADMINISTRACIÓN & BRANDING ---| */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/update-branding', [AdminSettingsController::class, 'updateBranding'])->name('update_branding');
        Route::patch('/settings/user/{user}', [AdminSettingsController::class, 'updateUser'])->name('user.update');
        Route::post('/reset-access', [AdminSettingsController::class, 'resetAccess'])->name('reset_access');
        Route::get('/roles/colors', [AdminSettingsController::class, 'editRoleColors'])->name('role_colors');
        Route::delete('/users/{user}/permissions', [AdminSettingsController::class, 'revokePermissions'])->name('users.revoke');
    });

    /* |--- MÓDULO DE ESTUDIANTES (CLIENTES) ---| */
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::resource('students', StudentController::class);

    /* |--- MÓDULO DE CIRCUITO MÉDICO ---| */
    
    // 1. Rutas Estáticas (Deben ir antes que los parámetros dinámicos {medical_exam})
    Route::get('/medical-exams/history', [MedicalExamController::class, 'history'])->name('medical_exams.history');

    // 2. Rutas de Acción Específicas
    // IMPORTANTE: El nombre del parámetro '{medical_exam}' debe ser idéntico al del Controlador
    Route::get('/medical-exams/{medical_exam}/evaluate', [MedicalExamController::class, 'evaluate'])->name('medical_exams.evaluate');
    Route::post('/medical-exams/{medical_exam}/result', [MedicalExamController::class, 'storeResult'])->name('medical_exams.store_result');
    Route::get('/medical-exams/{medical_exam}/report', [MedicalExamController::class, 'generateReport'])->name('medical_exams.report');
    Route::patch('/medical-exams/{medical_exam}/finish', [MedicalExamController::class, 'finish'])->name('medical_exams.finish');

    // 3. Recurso Base (Mantiene index, create, show, etc.)
    Route::resource('medical-exams', MedicalExamController::class)
        ->parameters(['medical-exams' => 'medical_exam'])
        ->names('medical_exams');

});

require __DIR__.'/auth.php';