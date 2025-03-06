<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si la solicitud espera una respuesta JSON, devuelve null
        if ($request->expectsJson()) {
            return null;
        }

        // Si no, devuelve una respuesta JSON con un mensaje de error
        return response()->json([
            'message' => 'No autenticado.',
        ], 401); // Código de estado HTTP 401: No autorizado
    }
}
