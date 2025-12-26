<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');    // requerido
            $table->string('documento'); // requerido
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->timestamps();

            // Evita duplicados del mismo documento dentro del mismo permiso
            $table->unique(['permiso_id', 'documento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
