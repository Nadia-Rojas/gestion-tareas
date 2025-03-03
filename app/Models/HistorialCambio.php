<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    protected $fillable = ['tarea_id', 'usuario_id', 'cambio', 'fecha_cambio'];

    // Relación uno a muchos inversa con Tarea
    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    // Relación uno a muchos inversa con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
