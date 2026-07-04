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
        Schema::create('novedads', function (Blueprint $table) {
            $table->id();

            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));

            $table->text('descripcion')->nullable();
            $table->string('subidopor')->nullable();

            $table->foreignId('tipo_novedad_id')
                ->nullable()
                ->constrained('tipo_novedads')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * ✅ CORRECCIÓN: el método down() original hacía dropIfExists('novedades')
     * con "es" al final — nombre incorrecto. La tabla real se llama 'novedads',
     * por lo que nunca se eliminaba al revertir y quedaba huérfana en la BD.
     */
    public function down(): void
    {
        Schema::dropIfExists('novedads');
    }
};
