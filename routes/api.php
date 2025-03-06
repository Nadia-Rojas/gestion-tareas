<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\HistorialCambioController;

Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('estado', EstadoController::class);
Route::apiResource('prioridad', PrioridadController::class);
Route::apiResource('tareas', TareaController::class);
Route::apiResource('historial-cambios', HistorialCambioController::class);

Route::get('/tareas', [TareaController::class, 'index']);
Route::post('/tareas', [TareaController::class, 'store']);
Route::put('/tareas/{id}', [TareaController::class, 'update']);
Route::delete('/tareas/{id}', [TareaController::class, 'destroy']);
