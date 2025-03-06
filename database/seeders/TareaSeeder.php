<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarea;

class TareaSeeder extends Seeder
{
    public function run()
    {
        Tarea::create([
            'titulo' => 'Tarea de prueba',
            'descripcion' => 'Descripción de prueba',
            'estado_id' => 1,  // Asume que ya tienes  en la base de datos
            'prioridad_id' => 2,  // Asume que ya tienes prioridades en la base de datos
            'creador_id' => 1,  // Asume que tienes usuarios en la base de datos
        ]);
    }
}
