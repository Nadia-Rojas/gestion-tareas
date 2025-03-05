<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use App\Http\Requests\TareaRequest;

class TareaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tareas = Tarea::all();
        return response()->json($tareas);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TareaRequest $request)
    {
        $validatedData = $request->validated();
        $tarea = Tarea::create($validatedData);
        return response()->json($tarea, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Tarea $tarea)
    {
        return response()->json($tarea);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TareaRequest $request, Tarea $tarea)
{
    $validatedData = $request->validated();
    $tarea->update($validatedData);
    return response()->json($tarea);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarea $tarea)
    {
        $tarea->delete();
        return response()->json(null, 204);
    }

}
