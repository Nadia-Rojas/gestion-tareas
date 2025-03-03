<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = ['descripcion'];

    // Relación uno a muchos con Tarea
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}
