<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado'; // Definir el nombre correcto de la tabla
    protected $fillable = ['descripcion'];

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}

