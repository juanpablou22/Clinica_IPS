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
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    // Actualización de Logo y Nombre de la I.P.S.
    Route::post('/admin/settings/update-branding', [AdminSettingsController::class, 'updateBranding'])->name('admin.update_branding');
    Route::patch('/admin/settings/user/{user}', [AdminSettingsController::class, 'updateUser'])->name('admin.user.update');
    Route::post('/admin/reset-access', [AdminSettingsController::class, 'resetAccess'])->name('admin.reset_access');
    Route::get('/admin/roles/colors', [AdminSettingsController::class, 'editRoleColors'])->name('admin.role_colors');
    Route::delete('/admin/users/{user}/permissions', [AdminSettingsController::class, 'revokePermissions'])->name('admin.users.revoke');


    /* |--- MÓDULO DE ESTUDIANTES (CLIENTES) ---| */
    // Buscador dinámico para agilizar atención
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::resource('students', StudentController::class);


    /* |--- MÓDULO DE CIRCUITO MÉDICO ---| */
    // Historial de Pacientes
    Route::get('/medical-exams/history', [MedicalExamController::class, 'history'])->name('medical_exams.history');

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

    // Rutas de Gestión de Resultados y Evaluación
    Route::post('/medical-exams/{medical_exam}/result', [MedicalExamController::class, 'storeResult'])->name('medical_exams.store_result');
    Route::patch('/medical-exams/{medical_exam}/finish', [MedicalExamController::class, 'finish'])->name('medical_exams.finish');
    Route::get('/medical-exams/{medical_exam}/evaluate', [MedicalExamController::class, 'evaluate'])->name('medical_exams.evaluate');

    // NUEVA RUTA: Generación de Reporte PDF (DomPDF)
    Route::get('/medical-exams/{medical_exam}/report', [MedicalExamController::class, 'generateReport'])->name('medical_exams.report');
});

require __DIR__.'/auth.php';
