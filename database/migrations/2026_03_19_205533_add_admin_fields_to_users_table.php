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
        Schema::table('users', function (Blueprint $table) {
            // Añadimos los campos necesarios para la configuración de Snake_DEV
            $table->string('job_title')->nullable()->after('email'); // Para el cargo (ej. Odontólogo)
            $table->string('ui_color')->default('#3b82f6')->after('job_title'); // Para el color de identidad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos los campos en caso de hacer rollback
            $table->dropColumn(['job_title', 'ui_color']);
        });
    }
};
