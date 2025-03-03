<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('cambio'); // Ejemplo: "Estado cambiado de Pendiente a En Progreso"
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('historial_cambios');
    }
};

