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
        Schema::create('nomenclaturas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 50); 
            $table->integer('piso')->nullable();
            $table->string('modulo')->nullable();
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomenclaturas');
    }
};
