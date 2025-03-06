<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tarea_usuarios', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->boolean('completado')->default(false);
            $table->unique(['tarea_id', 'usuario_id']); // Restricción única para evitar duplicados
        });
    }

    public function down() {
        Schema::dropIfExists('tarea_usuarios');
    }
};

