<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Orquestador principal para poblar la base de datos de la Clínica.
     */
    public function run(): void
    {
        // 1. Ejecutamos la creación de roles (Admisión, Odontología, etc.)
        $this->call(RoleSeeder::class);

        // 2. Buscamos el ID del rol Administrador para asignarlo al usuario
        $adminRole = Role::where('name', 'Administrador')->first();

        // 3. Creamos el usuario principal de acceso al sistema
        User::factory()->create([
            'name' => 'Juan Pablo - SnakeDev',
            'email' => 'admin@clinicaips.com',
            'password' => Hash::make('admin1234'), // Contraseña profesional encriptada
            'role_id' => $adminRole->id,
        ]);

        // Opcional: Si necesitas usuarios de prueba para otros roles en el futuro,
        // puedes agregarlos aquí siguiendo la misma lógica.
    }
}
