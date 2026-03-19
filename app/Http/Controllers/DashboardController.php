<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
// IMPORTANTE: Si aún no creas este modelo, el error 500 seguirá.
use App\Models\MedicalExam; 

class DashboardController extends Controller
{
    public function index()
    {
        // Si la tabla medical_exams no existe en la DB, fallará.
        // Para probar que el Dashboard cargue, puedes comentar las líneas de MedicalExam
        $totalPacientes = Student::count();
        
        // Verifica si la tabla existe antes de contar para evitar el Error 500
        try {
            $totalCertificados = MedicalExam::where('status', 'completado')->count();
            $pendientes = MedicalExam::where('status', '!=', 'completado')->count();
        } catch (\Exception $e) {
            // Si falla porque no hay tabla, ponemos 0 para que la web cargue
            $totalCertificados = 0;
            $pendientes = 0;
        }

        return view('dashboard', compact('totalPacientes', 'totalCertificados', 'pendientes'));
    }
}