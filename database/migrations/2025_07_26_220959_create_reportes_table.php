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
        Schema::create('reportes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subidopor');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->text('descripcion');
            $table->json('imagenes')->nullable();

            $table->foreignId('categoria_reporte_id')->nullable()->constrained('categorias_reporte')->onDelete('set null');
            $table->foreignId('tipo_reporte_id')->nullable()->constrained('tipo_reportes')->onDelete('set null');
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->onDelete('set null');
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicacions')->onDelete('set null');
            $table->foreignId('prioridad_id')->nullable()->constrained('prioridads')->onDelete('set null');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null');
            $table->foreignId('local_id')->nullable()->constrained('locals')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
