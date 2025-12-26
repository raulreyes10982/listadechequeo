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
        Schema::create('reporte_tecnicos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('fecha');
            $table->time('hora');
            $table->text('descripcion')->nullable();
            $table->foreignId('equipo_id')->nullable()->constrained('equipos')->nullOnDelete();
            $table->foreignId('tipo_intervencion_id')->nullable()->constrained('tipo_intervencions')->nullOnDelete();
            $table->string('subidopor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_tecnicos');
    }
};
