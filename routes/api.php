<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HistorialCambioController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuthController; // Si tienes un controlador de autenticación


Route::post('/login', [AuthController::class, 'login']);

// Rutas de recursos (incluyen update, store, etc.)
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('estado', EstadoController::class);
Route::apiResource('prioridad', PrioridadController::class);
Route::apiResource('tareas', TareaController::class);
Route::apiResource('historial-cambios', HistorialCambioController::class);

/* Rutas comentadas para tareas
Route::get('/tareas', [TareaController::class, 'index']);
Route::post('/tareas', [TareaController::class, 'store']);
Route::put('/tareas/{id}', [TareaController::class, 'update']);
Route::delete('/tareas/{id}', [TareaController::class, 'destroy']);
*/

// Rutas protegidas por middleware (si deseas que algunas operaciones requieran autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tareas', [TareaController::class, 'store']);  // Crear tarea
    Route::get('/tareas', [TareaController::class, 'index']);   // Listar tareas
    Route::put('/tareas/{tarea}', [TareaController::class, 'update']); // Editar tarea
    Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy']); // Eliminar tarea

    // Ruta para asignar usuarios, usando el mismo parámetro {tarea}
    Route::post('/tareas/{tarea}/asignar', [TareaController::class, 'asignarUsuarios']); // Asignar usuarios
    Route::post('/tareas/{tarea}/completar', [TareaController::class, 'confirmarCompletado']); // Marcar completada
});


// Ruta de login
Route::post('login', function (Request $request) {
    dd($request->all()); // Esto te ayudará a verificar si los datos del cuerpo llegan correctamente
    $usuario = Usuario::where('email', $request->email)->first();

    if (!$usuario || !Hash::check($request->password, $usuario->password)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $token = $usuario->createToken('AppName')->plainTextToken;

    return response()->json(['token' => $token]);
});





