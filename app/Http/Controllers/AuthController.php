<?php

namespace App\Http\Controllers;

use App\Models\Usuario; // Asegúrate de usar el modelo de Usuario
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Método de login
    public function login(Request $request)
    {
        // Validar las credenciales
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Asegúrate de que el email sea válido
            'password' => 'required', // Asegúrate de que la contraseña esté presente
        ]);

        // Si la validación falla, devolver un error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buscar al usuario por el correo electrónico
        $usuario = Usuario::where('email', $request->email)->first();

        // Si el usuario no existe o la contraseña es incorrecta
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        // Generar el token para el usuario
        $token = $usuario->createToken('YourAppName')->plainTextToken;

        // Devolver el token en la respuesta
        return response()->json(['token' => $token], 200);
    }
}

