<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradors', function (Blueprint $table) {
            $table->id();

            // 👇 USUARIO OPCIONAL
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nombre', 250)->nullable();
            $table->string('apellido', 250)->nullable();
            $table->bigInteger('celular')->nullable();
            $table->bigInteger('documento')->nullable();
            $table->string('lugarnacimiento', 250)->nullable();
            $table->integer('telefono')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('edad')->nullable();
            $table->string('barrio', 250)->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('correo_corporativo', 250)->unique();
            $table->string('correo_personal')->unique();
            $table->date('fechainiciolab')->nullable();
            $table->date('fechafinlab')->nullable();

            $table->foreignId('tipo_documento_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estado_civil_id')->constrained()->cascadeOnDelete();
            $table->foreignId('departamento_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tipo_contrato_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genero_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grupo_sanguineo_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradors');
    }
};
