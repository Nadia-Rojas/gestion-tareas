<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'tareas'; // Asegura que usa la tabla correcta

    protected $fillable = ['titulo', 'descripcion', 'estado_id', 'prioridad_id', 'creador_id'];

    // Relación muchos a muchos con Usuario
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'tarea_usuarios') // Especificamos la tabla pivote
                    ->withPivot('completado') // Si la tabla pivote tiene otros campos
                    ->withTimestamps(); // Si quieres que se guarden las fechas de creación/actualización
    }

    // Relación uno a muchos inversa con Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // Relación uno a muchos inversa con Prioridad
    public function prioridad()
    {
        return $this->belongsTo(Prioridad::class, 'prioridad_id');
    }

    // Relación uno a muchos inversa con Usuario (creador)
    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }
}
