<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla maestra del circuito médico optimizada para SnakeDev.
     */
    public function up(): void
    {
        Schema::create('medical_exams', function (Blueprint $table) {
            $table->id();

            // Relación con el estudiante (Paciente)
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('restrict');

            // Relación con el usuario que crea la orden (Secretaria/Admisión)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            /**
             * Áreas Solicitadas (JSON)
             * Ejemplo: ["odontologia", "psicologia", "medicina_general"]
             */
            $table->json('requested_areas')->nullable();

            /**
             * RESULTADOS DE VALORACIÓN MÉDICA (JSON)
             * Aquí guardaremos: peso, talla, imc, imc_status, 
             * respuestas del cuestionario y notas del examen físico.
             */
            $table->json('results')->nullable();

            // Estado global del circuito: 'pendiente', 'en_proceso', 'completado'
            $table->string('status')->default('pendiente')->index();

            // Resumen final del diagnóstico (llenado al finalizar todo el circuito)
            $table->text('observations')->nullable();
            $table->string('result_type')->nullable(); // Ej: Apto, No Apto

            $table->timestamps();

            // Índice compuesto para auditoría rápida
            $table->index(['user_id', 'created_at']);
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