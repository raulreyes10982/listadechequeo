<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verificacion_diarias', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->time('hora')->nullable(); // Oculto en el formulario pero se guarda
            $table->date('fecha')->nullable(); // Oculto en el formulario pero se guarda
            $table->string('verificadopor');  // Toma el nombre del usuario autenticado, oculto en el formulario pero se guarda
            $table->string('nombre');  // Almacena el nombre directamente como texto en la tabla trabajadores
            $table->string('documento');  // Almacena el documento directamente como texto
            $table->string('estado');  // Vigente o Vencido
            $table->integer('dias_autorizados')->nullable(); // Cálculo de días restantes de permiso
            $table->boolean('verificado')->default(false);  // Checkbox para confirmar la verificación

            $table->foreignId('colaborador_id')
                    ->nullable()
                    ->constrained('colaboradors')
                    ->nullOnDelete();

            $table->foreignId('permiso_id')
                    ->nullable()
                    ->constrained('permisos')
                    ->nullOnDelete();

            $table->foreignId('trabajador_id')
                    ->nullable()
                    ->constrained('trabajadores')
                    ->nullOnDelete();

            $table->timestamps();

            // Un registro por trabajador / permiso / día
            $table->unique(['permiso_id', 'trabajador_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verificacion_diarias');
    }
};
