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
    /**
     * Guarda una nueva tarea en la base de datos.
     */
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
    public function update(TareaRequest $request, Tarea $tarea)
    {
        try {
            $user = $request->user();

            if (
                !$user->roles()->where('descripcion', 'Administrador')->exists() &&
                !$tarea->usuariosAsignados()->where('usuario_id', $user->id)->exists()
            ) {
                return response()->json(['error' => 'No tienes permisos para editar esta tarea'], 403);
            }

            $tarea->update($request->validated());

            return response()->json($tarea);
        } catch (\Exception $e) {
            Log::error('Error al actualizar la tarea: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo actualizar la tarea'], 500);
        }
    }

    /**
     * Elimina una tarea.
     */
    public function destroy(Tarea $tarea)
    {
        try {
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
        // Obtener el usuario que realiza la asignación (por ejemplo, usuario con rol 'administrador')
        $usuarioId = $request->usuario_id; // Este es el ID del usuario que está haciendo la solicitud (Administrador)

        // Verificar si el usuario tiene el rol de 'administrador'
        $rol = DB::table('usuario_roles')
            ->join('roles', 'usuario_roles.rol_id', '=', 'roles.id')
            ->where('usuario_roles.usuario_id', $usuarioId)
            ->where('roles.descripcion', 'administrador') // Asegúrate de que el rol se llama exactamente 'administrador'
            ->first();

        if (!$rol) {
            return response()->json(['error' => 'No tienes permiso para asignar usuarios a esta tarea'], 403);
        }

        // Obtener los IDs de los usuarios a asignar
        $usuarios = $request->usuarios; // Un arreglo de IDs de usuarios a asignar

        // Verificar si los usuarios a asignar existen
        if (is_array($usuarios) && count($usuarios) > 0) {
            $usuariosExistentes = Usuario::whereIn('id', $usuarios)->get();  // Aquí usamos whereIn correctamente
            if ($usuariosExistentes->count() !== count($usuarios)) {
                return response()->json(['error' => 'Algunos usuarios no existen'], 400);
            }
        } else {
            return response()->json(['error' => 'El arreglo de usuarios no es válido'], 400);
        }

        // Asignar los usuarios a la tarea
        $tarea = Tarea::findOrFail($tareaId);
        $tarea->usuarios()->sync($usuarios); // Esto asigna múltiples usuarios a la tarea

        return response()->json(['mensaje' => 'Usuarios asignados correctamente'], 200);
    }


    public function eliminarUsuarios(Request $request, $tareaId)
    {
        // Obtener el usuario que realiza la acción
        $usuario = Usuario::find($request->usuario_id);

        // Verificar si el usuario existe
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario tiene el rol de 'administrador'
        $esAdmin = $usuario->roles()->where('descripcion', 'administrador')->exists();

        if (!$esAdmin) {
            return response()->json(['error' => 'No tienes permiso para eliminar usuarios de esta tarea'], 403);
        }

        // Validar que los usuarios a eliminar sean un array
        if (!is_array($request->usuarios) || empty($request->usuarios)) {
            return response()->json(['error' => 'Lista de usuarios no válida'], 400);
        }

        // Obtener la tarea y validar que exista
        $tarea = Tarea::find($tareaId);
        if (!$tarea) {
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        }

        // Eliminar los usuarios de la tarea
        $tarea->usuarios()->detach($request->usuarios);

        return response()->json(['mensaje' => 'Usuarios eliminados correctamente'], 200);
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
}
