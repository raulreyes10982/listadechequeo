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
        Schema::create('permisos', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('subidopor')->nullable();
            $table->date('fecha_inicio_trabajo');
            $table->date('fecha_fin_trabajo'); 
            $table->text('descripcion')->nullable();
            $table->text('actividad')->nullable(); 
            $table->json('tipo_actividad')->nullable();
            $table->string('archivo_pdf')->nullable();
            $table->foreignId('local_id')->nullable()->constrained('locals')->onDelete('set null');
            $table->foreignId('contratistas_id')->nullable()->constrained('contratistas')->onDelete('set null');
            $table->foreignId('tipo_permiso_id')->nullable()->constrained('tipo_permisos')->onDelete('set null');
            $table->foreignId('colaborador_id')->nullable()->constrained('colaboradors')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
