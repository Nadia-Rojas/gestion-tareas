<?php

namespace App\Http\Controllers;

use App\Models\Prioridad;  // Importar el modelo Prioridad
use Illuminate\Http\Request;

class PrioridadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Puedes devolver todas las prioridades si lo necesitas
        return Prioridad::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',  // Validación de la descripción
        ]);

        // Crear la nueva prioridad
        $prioridad = Prioridad::create([
            'descripcion' => $validated['descripcion'],
        ]);

        // Devolver la prioridad recién creada como respuesta
        return response()->json($prioridad, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Mostrar una prioridad específica por su ID
        return Prioridad::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',  // Validación de la descripción
        ]);

        // Buscar la prioridad y actualizarla
        $prioridad = Prioridad::findOrFail($id);
        $prioridad->update($validated);

        // Devolver la prioridad actualizada
        return response()->json($prioridad, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Eliminar una prioridad específica por su ID
        $prioridad = Prioridad::findOrFail($id);
        $prioridad->delete();

        // Responder con un mensaje de éxito
        return response()->json(['message' => 'Prioridad eliminada con éxito'], 200);
    }
}

