<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade la columna role_id a la tabla de usuarios existente.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Creamos la llave foránea que apunta a la tabla 'roles'
            // La ponemos después del email para mantener el orden visual
            $table->foreignId('role_id')
                  ->after('email')
                  ->nullable() // Permite nulos temporalmente para evitar conflictos
                  ->constrained('roles')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Elimina la columna y la relación si decidimos revertir la migración.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Primero eliminamos la restricción de llave foránea
            $table->dropForeign(['role_id']);
            // Luego eliminamos la columna
            $table->dropColumn('role_id');
        });
    }
};
