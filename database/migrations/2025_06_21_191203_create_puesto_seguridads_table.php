<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('puesto_seguridads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 250)->unique();      // Código único del puesto
            $table->string('puesto', 250);                // Nombre del puesto
            $table->time('inicio_hora')->nullable();      // Hora programada inicio
            $table->time('fin_hora')->nullable();         // Hora programada fin
            $table->string('descripcion', 250)->nullable(); // Observación

            // 🔹 Campos de QR
            $table->string('qr_token')->nullable();       // Token único generado
            $table->date('qr_expira')->nullable();        // Fecha de expiración (30 días)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_seguridads');
    }
};
