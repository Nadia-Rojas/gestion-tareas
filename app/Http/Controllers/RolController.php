<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los roles
        $roles = \App\Models\Rol::all();

        // Devolver la lista de roles
        return response()->json($roles, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de la solicitud
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear el nuevo rol
        $rol = \App\Models\Rol::create([
            'descripcion' => $request->descripcion,
        ]);

        // Devolver el rol recién creado
        return response()->json($rol, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    // Buscar el rol por ID
    $rol = \App\Models\Rol::findOrFail($id);

    // Devolver el rol encontrado
    return response()->json($rol, 200);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    // Validar los datos de la solicitud
    $request->validate([
        'descripcion' => 'required|string|max:255',
    ]);

    // Buscar el rol por ID
    $rol = \App\Models\Rol::findOrFail($id);

    // Actualizar la descripción del rol
    $rol->update([
        'descripcion' => $request->descripcion,
    ]);

    // Devolver el rol actualizado
    return response()->json($rol, 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Buscar el rol por ID
        $rol = \App\Models\Rol::findOrFail($id);

        // Eliminar el rol
        $rol->delete();

        // Devolver mensaje de éxito
        return response()->json(['message' => 'Rol eliminado'], 200);
    }

}
