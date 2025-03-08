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
        $user = $request->user();

        if ($user->roles()->where('descripcion', 'Administrador')->exists()) {
            $tareas = Tarea::with('usuariosAsignados')->get();
        } else {
            $tareas = Tarea::whereHas('usuariosAsignados', function ($q) use ($user) {
                $q->where('usuario_id', $user->id);
            })->with('usuariosAsignados')->get();
        }

        return response()->json($tareas, 200);
    }

    /**
     * Guarda una nueva tarea en la base de datos.
     */
    public function store(TareaRequest $request)
    {
        $tarea = Tarea::create($request->validated());
        return response()->json($tarea, 201);
    }

    /**
     * Muestra una tarea específica.
     */
    public function show(Tarea $tarea)
    {
        return response()->json($tarea);
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(TareaRequest $request, Tarea $tarea)
    {
        try {
            $user = $request->user();

            if (!$user->roles()->where('descripcion', 'Administrador')->exists() &&
                !$tarea->usuariosAsignados()->where('usuario_id', $user->id)->exists()) {
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
