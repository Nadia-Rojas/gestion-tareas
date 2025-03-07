<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Asegúrate de importar este trait

class Usuario extends Authenticatable
{
    protected $fillable = ['nombre', 'email', 'password'];
    use HasApiTokens; // Solo importa el trait HasApiTokens

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

