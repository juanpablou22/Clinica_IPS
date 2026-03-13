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
    Schema::create('medical_exams', function (Blueprint $table) {//creamos la tabla medical_exams
        $table->id();
        // Relación con el estudiante (Hijo)
        $table->foreignId('student_id')->constrained('students')->onDelete('restrict');
        // Relación con el usuario médico (Padre)
        $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
        $table->string('status')->default('pendiente');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_exams');
    }
};
