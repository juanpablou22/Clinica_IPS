<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('exam_results', function (Blueprint $table) {
        $table->id();
        // Relación con el examen maestro
        $table->foreignId('medical_exam_id')->constrained('medical_exams')->onDelete('cascade');
        $table->string('area'); // Optometría, Audiometría, etc.
        $table->json('data'); // Aquí guardaremos los resultados específicos
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
