<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    public function run()
    {
        DB::table('estado')->insert([
            ['id' => 1, 'descripcion' => 'Pendiente'],
            ['id' => 2, 'descripcion' => 'En Progreso'],
            ['id' => 3, 'descripcion' => 'Completada'],
        ]);

    }
}

