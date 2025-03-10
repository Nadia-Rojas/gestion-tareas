<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


use App\Http\Requests\TareaRequest;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TareaController extends Controller
{
    /**
     * Muestra todas las tareas.
     */
    public function index(Request $request)
    {
        // Validar que el user_id esté presente en la solicitud
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
        ]);

        // Obtener el usuario desde el request
        $userId = $request->user_id;

        // Buscar al usuario en la base de datos
        $user = Usuario::find($userId);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario tiene el rol de administrador
        $esAdmin = $user->roles()->where('descripcion', 'Administrador')->exists();

        // Construir la consulta base para las tareas
        $query = Tarea::with([
            'usuariosAsignados:id,nombre,email',
            'estado:id,descripcion',
            'prioridad:id,descripcion'
        ]);

        // Si el usuario no es administrador, solo mostrar sus tareas asignadas
        if (!$esAdmin) {
            $query->whereHas('usuariosAsignados', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            });
        }

        // Agregar paginación con 10 elementos por página (puedes ajustar el número)
        $tareas = $query->paginate(10);

        // Retornar las tareas en formato JSON
        return response()->json($tareas, 200);
    }




    /**
     * Guarda una nueva tarea en la base de datos.
     */
    public function store(TareaRequest $request)
    {
        // Validar que creador_id esté presente en la solicitud y exista en la tabla usuarios
        $request->validate([
            'creador_id' => 'required|exists:usuarios,id',
        ]);

        // Obtener los datos validados del request (incluyendo creador_id)
        $data = $request->validated();

        // Crear la tarea con los datos validados
        $tarea = Tarea::create($data);

        // Asignar automáticamente al creador en la tabla pivote
        $tarea->usuariosAsignados()->attach($request->creador_id);

        // Retornar la tarea creada en formato JSON
        return response()->json($tarea, 201);
    }


    /**
     * Muestra una tarea específica.
     */
    public function show(Request $request, Tarea $tarea)
    {
        // Validar que se envíe el user_id y que exista en la tabla usuarios
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
        ]);

        // Obtener el usuario
        $user = Usuario::find($request->user_id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si es administrador
        $esAdmin = $user->roles()->where('descripcion', 'Administrador')->exists();

        // Si NO es admin y NO está asignado a la tarea, no puede verla
        if (!$esAdmin && !$tarea->usuariosAsignados()->where('usuario_id', $user->id)->exists()) {
            return response()->json(['error' => 'No tienes permiso para ver esta tarea'], 403);
        }

        // Cargar relaciones para mostrar info completa de la tarea
        $tarea->load([
            'usuariosAsignados:id,nombre,email',
            'estado:id,descripcion',
            'prioridad:id,descripcion'
        ]);

        // Retornar la tarea con sus relaciones
        return response()->json($tarea, 200);
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(Request $request, Tarea $tarea)
    {
        try {
            // Validar si el ID de usuario se pasa en la solicitud
            $usuarioId = $request->input('usuario_id');

            if (!$usuarioId) {
                return response()->json(['error' => 'No se proporcionó el ID de usuario'], 400);
            }

            // Validar si la tarea existe
            if (!$tarea) {
                return response()->json(['error' => 'Tarea no encontrada'], 404);
            }

            // Comprobar si el usuario tiene permisos para editar la tarea
            $tareaUserAssigned = $tarea->usuariosAsignados()->where('usuario_id', $usuarioId)->exists();
            $isAdmin = $this->isUserAdmin($usuarioId);

            if (!$isAdmin && !$tareaUserAssigned) {
                return response()->json(['error' => 'No tienes permisos para editar esta tarea'], 403);
            }

            // Actualizar los datos de la tarea
            $tarea->titulo = $request->input('titulo');
            $tarea->descripcion = $request->input('descripcion');
            $tarea->estado_id = $request->input('estado_id');
            $tarea->prioridad_id = $request->input('prioridad_id');

            // Guardar la tarea
            if ($tarea->save()) {
                return response()->json([
                    'message' => 'Tarea actualizada correctamente',
                    'tarea' => $tarea
                ], 200);
            } else {
                return response()->json(['error' => 'No se pudo guardar la tarea'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error al actualizar la tarea: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo actualizar la tarea'], 500);
        }
    }

    private function isUserAdmin($userId)
    {
        $user = \App\Models\Usuario::find($userId);
        if (!$user) {
            return false;
        }
        // Verifica si el usuario tiene el rol de "Administrador"
        return $user->roles()->where('descripcion', 'Administrador')->exists();
    }







    /**
     * Elimina una tarea.
     */
    public function destroy(Request $request, Tarea $tarea)
{
    try {
        // Obtener el usuario autenticado
        $usuario = Usuario::find($request->usuario_id);

        // Verificar si el usuario tiene el rol de 'administrador'
        if (!$usuario || !$usuario->roles()->where('descripcion', 'administrador')->exists()) {
            return response()->json(['error' => 'No tienes permiso para eliminar tareas'], 403);
        }

        // Eliminar la tarea
        $tarea->delete();
        return response()->json(null, 204);
    } catch (\Exception $e) {
        Log::error('Error al eliminar la tarea: ' . $e->getMessage());
        return response()->json(['error' => 'No se pudo eliminar la tarea'], 500);
    }
}



    /**
     * Asigna usuarios a una tarea.
     */
    public function asignarUsuarios(Request $request, $tareaId)
    {
        // Verificar si el usuario que hace la solicitud es administrador
        $usuarioAdmin = Usuario::findOrFail($request->usuario_id);
        $esAdmin = DB::table('usuario_roles')
            ->join('roles', 'usuario_roles.rol_id', '=', 'roles.id')
            ->where('usuario_roles.usuario_id', $usuarioAdmin->id)
            ->where('roles.descripcion', 'administrador')
            ->exists();

        abort_unless($esAdmin, 403, 'No tienes permiso para asignar usuarios a esta tarea');

        // Buscar la tarea
        $tarea = Tarea::findOrFail($tareaId);

        // Validar que los usuarios sean un array y no estén vacíos
        $usuarios = $request->input('usuarios');
        if (!is_array($usuarios) || empty($usuarios)) {
            abort(400, 'El arreglo de usuarios no es válido');
        }

        // Verificar que todos los usuarios existen
        $usuariosExistentes = Usuario::whereIn('id', $usuarios)->pluck('id')->toArray();
        if (count($usuariosExistentes) !== count($usuarios)) {
            abort(400, 'Algunos usuarios no existen');
        }

        // Asignar usuarios a la tarea
        $tarea->usuarios()->sync($usuarios);

        return response()->json(['mensaje' => 'Usuarios asignados correctamente'], 200);
    }


    /**
     * Confirma que un usuario ha completado una tarea.
     */
    public function confirmarCompletado(Request $request, $tareaId)
    {
        // Obtener el ID del usuario desde el body de la petición
        $userId = $request->usuario_id;

        if (!$userId) {
            return response()->json(['error' => 'Usuario no especificado'], 400);
        }

        // Verificar que el usuario esté asignado a la tarea
        $registro = DB::table('tarea_usuarios')
            ->where('tarea_id', $tareaId)
            ->where('usuario_id', $userId)
            ->first();

        if (!$registro) {
            return response()->json(['error' => 'No estás asignado a esta tarea'], 403);
        }

        // Marcar la tarea como completada para este usuario
        DB::table('tarea_usuarios')
            ->where('tarea_id', $tareaId)
            ->where('usuario_id', $userId)
            ->update(['completado' => true]);

        // Verificar si todos los usuarios han confirmado completado
        $pendientes = DB::table('tarea_usuarios')
            ->where('tarea_id', $tareaId)
            ->where('completado', false)
            ->count();

        if ($pendientes === 0) {
            // Si no hay pendientes, actualizar el estado de la tarea a "completada"
            $estadoCompletada = $this->obtenerEstadoCompletada();
            Tarea::findOrFail($tareaId)->update(['estado_id' => $estadoCompletada]);
        }

        return response()->json(['mensaje' => 'Confirmación registrada'], 200);
    }


    /**
     * Obtiene el ID del estado "completada".
     */
    public  function obtenerEstadoCompletada()
    {
        $estado = \App\Models\Estado::where('descripcion', 'completada')->first();
        if (!$estado) {
            throw new \Exception("Estado 'completada' no definido en la base de datos.");
        }
        return $estado->id;
    }




    /**
 * Elimina un usuario asignado a una tarea.
 */
public function eliminarUsuarios(Request $request, $tareaId)
{
    // Validar que el usuario que hace la solicitud exista
    $usuarioAdmin = Usuario::findOrFail($request->usuario_id);

    // Verificar si el usuario es administrador
    $esAdmin = DB::table('usuario_roles')
        ->where('id_usuario', $usuarioAdmin->id)
        ->whereIn('id_rol', function ($query) {
            $query->select('id')->from('roles')->where('descripcion', 'administrador');
        })
        ->exists();

    abort_unless($esAdmin, 403, 'No tienes permiso para eliminar usuarios de esta tarea');

    // Buscar la tarea
    $tarea = Tarea::findOrFail($tareaId);

    // Validar que se reciba una lista de usuarios en el request
    $usuarios = $request->input('usuarios');
    if (!is_array($usuarios) || empty($usuarios)) {
        abort(400, 'Lista de usuarios no válida');
    }

    // Eliminar directamente los usuarios de la tarea usando detach()
    $tarea->usuarios()->detach($usuarios);

    return response()->json(['mensaje' => 'Usuarios eliminados correctamente'], 200);
}



}



