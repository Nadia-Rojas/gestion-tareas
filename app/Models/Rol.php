<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = ['descripcion'];

    // Relación muchos a muchos con Usuario
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_roles');
    }
}
