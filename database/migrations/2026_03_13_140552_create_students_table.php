<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Estructura optimizada para el sistema de matrícula y circuito médico.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Identificación (Tipo y Número Único)
            $table->string('document_type');      // RC, TI, CC
            $table->string('document_number')->unique();

            // Datos Personales
            $table->string('first_name');
            $table->string('last_name');

            // Datos Académicos
            $table->string('grade');              // Grado escolar (ej: 5° Primaria)

            $table->timestamps();

            // Índices de optimización SnakeDev
            $table->index('document_number');     // Búsqueda rápida por documento
            $table->index(['last_name', 'first_name']); // Búsqueda rápida por nombre completo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
