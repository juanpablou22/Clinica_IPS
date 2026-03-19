<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta tabla es el contenedor universal para todos los especialistas de SnakeDev.
     */
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();

            // Relación con el examen maestro (El circuito del estudiante)
            $table->foreignId('medical_exam_id')
                  ->constrained('medical_exams')
                  ->onDelete('cascade');

            // Especialista que realizó la evaluación (Auditoría Médica)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            // Área médica: optometria, odontologia, etc. (En minúsculas para facilitar filtros)
            $table->string('area')->index();

            /**
             * Resultados específicos en formato JSON.
             * Ejemplo para Optometría: {"ojo_izquierdo": "20/20", "ojo_derecho": "20/40", "observacion": "Usar lentes"}
             * Ejemplo para Odontología: {"caries": [14, 15, 18], "higiene": "buena"}
             */
            $table->json('data');

            // Observaciones generales de esta área específica
            $table->text('notes')->nullable();

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
