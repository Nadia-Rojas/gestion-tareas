<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tarea_usuarios', function (Blueprint $table) {
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->boolean('completado')->default(false);
            $table->primary(['tarea_id', 'usuario_id']);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tarea_usuarios');
    }
};

