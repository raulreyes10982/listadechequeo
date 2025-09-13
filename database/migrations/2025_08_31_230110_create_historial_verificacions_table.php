<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historial_verificacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('verificadopor')->nullable();
            $table->string('nombre')->nullable();
            $table->string('documento')->nullable();
            $table->string('estado')->nullable();
            $table->integer('dias_autorizados')->nullable();
            $table->boolean('verificado')->default(false);
            $table->foreignId('local_id')->nullable()->constrained('locals')->nullOnDelete();
            $table->foreignId('contratistas_id')->nullable()->constrained('contratistas')->nullOnDelete();
            $table->foreignId('permiso_id')->nullable()->constrained('permisos')->nullOnDelete();
            $table->foreignId('trabajador_id')->nullable()->constrained('trabajadores')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_verificacions');
    }
};
