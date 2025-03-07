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

/* Rutas comentadas para tareas*/
Route::get('/tareas', [TareaController::class, 'index']);
Route::post('/tareas', [TareaController::class, 'store']);
Route::put('/tareas/{id}', [TareaController::class, 'update']);
Route::delete('/tareas/{id}', [TareaController::class, 'destroy']);
Route::post('/tareas/{tareaId}/confirmar-completado', [TareaController::class, 'confirmarCompletado']);
Route::post('/tareas/{tareaId}/asignar-usuarios', [TareaController::class, 'asignarUsuarios']);
Route::get('/estado-completado', [TareaController::class, 'obtenerEstadoCompletada']);




