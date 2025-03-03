<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->foreignId('estado_id')->constrained('estado')->onDelete('cascade');
            $table->foreignId('prioridad_id')->constrained('prioridad')->onDelete('cascade');
            $table->foreignId('creador_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('tareas');
    }
};

