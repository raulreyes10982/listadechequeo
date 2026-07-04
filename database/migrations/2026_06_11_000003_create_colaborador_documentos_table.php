<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaborador_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colaborador_id')->constrained('colaboradors')->cascadeOnDelete();
            $table->string('nombre');           // ej: "Cédula", "Contrato firmado"
            $table->string('archivo');          // ruta en storage
            $table->string('tipo')->nullable(); // pdf, jpg, etc (informativo)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaborador_documentos');
    }
};
