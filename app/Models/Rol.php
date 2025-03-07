<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $fillable = ['descripcion'];
  // Definir explícitamente el nombre de la tabla
  protected $table = 'roles';
    // Relación muchos a muchos con Usuario
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_roles');
    }
}
