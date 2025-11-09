<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('verificacion_turnos', function (Blueprint $table) {
            $table->id();

            // Turno relacionado
            $table->foreignId('registrar_turno_id')
                ->constrained('registrar_turnos')
                ->cascadeOnDelete();

            // Tipo de evento
            $table->enum('tipo', ['ingreso', 'ronda', 'salida', 'reemplazo'])
                ->default('ingreso');

            // Hora y observaciones
            $table->timestamp('hora_verificacion')->nullable();
            $table->text('observacion')->nullable();

            // Usuario verificador
            $table->foreignId('verificado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Estado
            $table->enum('estado', ['pendiente', 'verificado', 'cerrado'])
                ->default('pendiente');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('verificacion_turnos');
    }
};
