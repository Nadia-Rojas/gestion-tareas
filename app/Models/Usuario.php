<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $fillable = ['nombre', 'email', 'password'];

    // Relación muchos a muchos con Tarea
    public function tareas()
    {
        return $this->belongsToMany(Tarea::class, 'tarea_usuarios')
                    ->withPivot('completado')
                    ->withTimestamps();
    }

    // Relación muchos a muchos con Rol
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_roles');
    }
}

