<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_estado_reportes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cambiado_por', 255);
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->text('descripcion')->nullable();
            $table->foreignId('reporte_tecnico_id')->constrained('reporte_tecnicos')->onDelete('cascade');
            $table->foreignId('estado_reporte_id')->constrained('estado_reportes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estado_reportes');
    }
};
