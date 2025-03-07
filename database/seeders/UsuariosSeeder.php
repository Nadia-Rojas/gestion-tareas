<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            [
                'nombre' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
            ],
            [
                'nombre' => 'Usuario 1',
                'email' => 'usuario1@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'nombre' => 'Usuario 2',
                'email' => 'usuario2@example.com',
                'password' => Hash::make('password123'),
            ]
        ]);
    }
}
