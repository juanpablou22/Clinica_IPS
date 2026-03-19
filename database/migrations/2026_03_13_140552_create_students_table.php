<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // --- DATOS DEL ESTUDIANTE ---
            $table->string('document_type');      // RC, TI, CC
            $table->string('document_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('age');               // Nueva: Edad del niño
            $table->string('gender');            // Nueva: Sexo
            $table->string('previous_school');    // Nueva: Colegio anterior
            $table->string('grade');              // Grado al que aplica

            // --- DATOS DEL ACUDIENTE (Integrados) ---
            $table->string('guardian_name');
            $table->string('guardian_lastname');
            $table->string('guardian_document');
            $table->integer('guardian_age');
            $table->string('guardian_phone');
            $table->string('guardian_address');
            $table->string('guardian_relationship'); // Parentesco
            $table->string('guardian_email');

            $table->timestamps();

            // Índices SnakeDev para velocidad en TiDB
            $table->index('document_number');
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
