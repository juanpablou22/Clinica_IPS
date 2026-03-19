<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla maestra del circuito médico: conecta al estudiante con las áreas solicitadas.
     * Optimizada para SnakeDev y TiDB Cloud.
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
             * Ejemplo: ["odontologia", "psicologia", "optometria"]
             * Nota: TiDB maneja JSON de forma nativa y eficiente.
             */
            $table->json('requested_areas')->nullable();

            // Estado global del circuito: 'pendiente', 'en_proceso', 'completado'
            // Usamos index() para que la bandeja de entrada cargue más rápido.
            $table->string('status')->default('pendiente')->index();

            // Resumen final del diagnóstico (llenado al finalizar todo el circuito)
            $table->text('observations')->nullable();
            $table->string('result_type')->nullable(); // Ej: Apto, No Apto, Apto con restricciones

            $table->timestamps();

            // Índice compuesto para auditoría rápida: ¿Qué órdenes creó X usuario hoy?
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
