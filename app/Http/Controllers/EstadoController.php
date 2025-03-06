<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        return response()->json(Estado::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $estado = Estado::create($request->all());
        return response()->json($estado, 201);
    }

    public function show(string $id)
    {
        return response()->json(Estado::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $estado = Estado::findOrFail($id);
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $estado->update($request->all());
        return response()->json($estado);
    }

    public function destroy(string $id)
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();
        return response()->json(['message' => 'Estado eliminado correctamente']);
    }
}

