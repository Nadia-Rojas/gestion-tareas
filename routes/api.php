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
Route::apiResource('estados', EstadoController::class);
Route::apiResource('prioridades', PrioridadController::class);
Route::apiResource('tareas', TareaController::class);
Route::apiResource('historial-cambios', HistorialCambioController::class);

