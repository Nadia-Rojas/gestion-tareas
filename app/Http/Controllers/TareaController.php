<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TareaRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TareaController extends Controller
{
    /**
     * Muestra todas las tareas.
     */
    public function index()
    {
        try {
            $tareas = Tarea::all();
            return response()->json($tareas);
        } catch (\Exception $e) {
            Log::error('Error al obtener las tareas: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudieron obtener las tareas'], 500);
        }
    }

    /**
     * Guarda una nueva tarea en la base de datos.
     */
    public function store(Request $request)
    {
        $tarea = Tarea::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado_id' => $request->estado_id,
            'prioridad_id' => $request->prioridad_id,
            'creador_id' => $request->creador_id,
        ]);

        return response()->json($tarea, 201);
    }


    /**
     * Muestra una tarea específica.
     */
    public function show(Tarea $tarea)
    {
        try {
            return response()->json($tarea);
        } catch (ModelNotFoundException $e) {
            Log::error('Tarea no encontrada: ' . $e->getMessage());
            return response()->json(['error' => 'Tarea no encontrada'], 404);
        } catch (\Exception $e) {
            Log::error('Error al obtener la tarea: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo obtener la tarea'], 500);
        }
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(TareaRequest $request, Tarea $tarea)
    {
        try {
            // Validar los datos recibidos
            $validatedData = $request->validated();

            // Actualizar tarea
            $tarea->update($validatedData);

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

public function asignarUsuarios($id)
{
    // Lógica para asignar usuarios a la tarea
    $tarea = Tarea::find($id);

    if (!$tarea) {
        return response()->json(['error' => 'Tarea no encontrada'], 404);
    }

    // Asignar usuarios o realizar la acción correspondiente
    return response()->json(['success' => 'Usuarios asignados a la tarea'], 200);
}


}



