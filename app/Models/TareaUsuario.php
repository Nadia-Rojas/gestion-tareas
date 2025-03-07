<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TareaUsuario extends Model
{
    use HasFactory;
    protected $table = 'tarea_usuarios';
    protected $fillable = [
        'tarea_id',
        'usuario_id',
        'completado'
    ];
}
