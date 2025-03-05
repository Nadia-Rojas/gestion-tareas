<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\TareaRequest;

class TareaController extends Controller
{
    /**
     * Muestra todas las tareas.
     */
    public function index()
    {
        $tareas = Tarea::all();
        return response()->json($tareas);
    }

    /**
     * Guarda una nueva tarea en la base de datos.
     */
    public function store(TareaRequest $request)
    {
        // Verifica qué datos llegan
        Log::info('Datos recibidos:', $request->all());

        // Valida los datos
        $validatedData = $request->validated();

        // Intenta crear la tarea
        $tarea = Tarea::create($validatedData);

        // Verifica si la tarea se creó
        if (!$tarea) {
            return response()->json(['error' => 'No se pudo crear la tarea'], 500);
        }

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
        $validatedData = $request->validated();
        $tarea->update($validatedData);
        return response()->json($tarea);
    }

    /**
     * Elimina una tarea.
     */
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return response()->json(null, 204);
    }
}
