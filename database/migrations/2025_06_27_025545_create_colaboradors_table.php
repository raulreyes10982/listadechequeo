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
        Schema::create('colaboradors', function (Blueprint $table) {
            $table->bigIncrements('id');
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

                $table->foreignId('tipo_documento_id')->constrained('tipo_documentos')->onDelete('cascade');
                $table->foreignId('estado_civil_id')->constrained('estado_civils')->onDelete('cascade');
                $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade');
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->foreignId('cargo_id')->constrained('cargos')->onDelete('cascade');
                $table->foreignId('tipo_contrato_id')->constrained('tipo_contratos')->onDelete('cascade');
                $table->foreignId('genero_id')->constrained('generos')->onDelete('cascade');
                $table->foreignId('grupo_sanguineo_id')->constrained('grupo_sanguineos')->onDelete('cascade');    
                $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colaboradors');
    }
};
