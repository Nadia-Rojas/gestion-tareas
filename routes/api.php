<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HistorialCambioController;

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
    Route::put('/tareas/{id}', [TareaController::class, 'update']); // Editar tarea
    Route::delete('/tareas/{id}', [TareaController::class, 'destroy']); // Eliminar tarea

    // Decide cuál ruta para asignar usuarios prefieres usar
    Route::post('/tareas/{id}/asignar', [TareaController::class, 'asignarUsuarios']); // Asignar usuarios
    Route::post('/tareas/{id}/completar', [TareaController::class, 'confirmarCompletado']); // Marcar completada
});

// Ruta pública o redundante para asignar usuarios (revisa si es necesaria)
//Route::post('/tareas/{id}/asignar-usuarios', [TareaController::class, 'asignarUsuarios']);

// Ruta explícita para actualizar usuarios (aunque ya está incluida en apiResource)
//Route::put('usuarios/{id}', [UsuarioController::class, 'update']);
