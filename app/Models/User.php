<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password'];

    protected $hidden = ['password'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_roles', 'usuario_id', 'rol_id');
    }

    public function tareasAsignadas()
    {
        return $this->belongsToMany(Tarea::class, 'tarea_usuarios', 'usuario_id', 'tarea_id')->withPivot('completado');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'creador_id');
    }
}


