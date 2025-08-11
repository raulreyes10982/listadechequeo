<?php

// database/migrations/xxxx_xx_xx_create_bitacora_estados_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bitacora_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporte_id')->constrained()->onDelete('cascade');
            $table->foreignId('estado_id')->constrained('estados')->onDelete('restrict');
            $table->string('descripcion')->nullable();
            $table->string('registrado_por');
            $table->date('fecha');
            $table->time('hora');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bitacora_estados');
    }
};

