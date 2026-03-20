<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalExamController extends Controller
{
    // ... (Mantén los métodos index, history, evaluate que limpiamos antes)

    public function storeResult(Request $request, MedicalExam $medical_exam)
    {
        $area = Str::slug(Auth::user()->role->name, '_');

        return DB::transaction(function () use ($medical_exam, $area, $request) {
            // Lógica para Odontología o General
            $data = ($area === 'odontologia') 
                ? [
                    'odontograma' => $request->results,
                    'habitos' => $request->habitos ?? [],
                    'odontograma_imagen' => $request->odontograma_imagen
                  ]
                : $request->except(['_token', 'notes']);

            $medical_exam->results()->updateOrCreate(
                ['area' => $area],
                [
                    'user_id' => Auth::id(),
                    'data'    => $data,
                    'notes'   => $request->notes ?? 'Evaluación realizada.',
                ]
            );

            // Cierre automático
            if ($medical_exam->results()->count() === collect($medical_exam->requested_areas)->count()) {
                $medical_exam->update(['status' => 'completado']);
            }

            return redirect()->route('medical_exams.index')->with('success', 'Guardado.');
        });
    }
}