<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seguimiento_reportes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reporte_id')->constrained()->onDelete('cascade');
            $table->foreignId('estado_id')->constrained()->onDelete('restrict'); // Estado del seguimiento
            $table->text('descripcion')->nullable(); // Comentario o seguimiento
            $table->string('registrado_por'); // Nombre del usuario que hizo el cambio
            $table->date('fecha')->default(now());
            $table->time('hora')->default(now());

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento_reportes');
    }
};
