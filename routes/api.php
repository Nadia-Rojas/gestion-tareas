<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HistorialCambioController;

// Rutas de recursos (CRUD automático)
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('estado', EstadoController::class);
Route::apiResource('prioridad', PrioridadController::class);
Route::apiResource('tareas', TareaController::class);
Route::apiResource('historial-cambios', HistorialCambioController::class);

// Rutas adicionales de tareas
Route::post('/tareas/{tareaId}/confirmar-completado', [TareaController::class, 'confirmarCompletado']);
Route::post('/tareas/{tareaId}/asignar-usuarios', [TareaController::class, 'asignarUsuarios']);
Route::post('/tareas/{tareaId}/eliminar-usuarios', [TareaController::class, 'eliminarUsuarios']);
Route::get('/estado-completado', [TareaController::class, 'obtenerEstadoCompletada']);

// Rutas adicionales de usuarios
Route::post('/usuarios/{id}/asignar-rol', [UsuarioController::class, 'asignarRol']);









