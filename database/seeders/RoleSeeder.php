<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Registra los roles oficiales para el personal de la clínica.
     */
    public function run(): void
    {
        $roles = [
            'Admisión',         // Encargado de recibir estudiantes
            'Medicina General',
            'Optometría',
            'Odontología',
            'Psicología',
            'Fonoaudiología',
            'Audiometría',
            'Administrador'      // Acceso total al sistema
        ];

        foreach ($roles as $roleName) {
            Role::create([
                'name' => $roleName
            ]);
        }
    }
}
