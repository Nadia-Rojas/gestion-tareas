<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function update(Request $request, $id)
    {
        // Buscar el usuario por su ID
        $usuario = Usuario::findOrFail($id);

        // Definir las reglas de validación
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Regla para garantizar que el email sea único, ignorando el del usuario actual
                Rule::unique('usuarios')->ignore($usuario->id),
            ],
            'password' => 'nullable|string|min:8',
        ]);

        // Si se proporciona una nueva contraseña, encriptarla
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            // Si no se proporciona una nueva contraseña, eliminarla del array validado
            unset($validatedData['password']);
        }

        // Actualizar el usuario con los datos validados
        $usuario->update($validatedData);

        // Retornar una respuesta JSON con el usuario actualizado
        return response()->json($usuario);
    }
}
