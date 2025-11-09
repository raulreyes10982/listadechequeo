<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrar_turnos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fin');
            $table->text('observacion')->nullable();
            $table->foreignId('puesto_seguridad_id')->constrained('puesto_seguridads')->cascadeOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradors')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrar_turnos');
    }
};
