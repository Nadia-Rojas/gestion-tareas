<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('prioridad')->insert([
            ['id' => 1, 'descripcion' => 'Baja'],
            ['id' => 2, 'descripcion' => 'Media'],
            ['id' => 3, 'descripcion' => 'Alta'],
        ]);

    }
}
