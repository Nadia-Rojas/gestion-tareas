<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            'nombre' => 'Usuario de prueba',
            'email' => 'usuario@correo.com',
            'password' => Hash::make('123456'), // Encripta la contraseña
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
