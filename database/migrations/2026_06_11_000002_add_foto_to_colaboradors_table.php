<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colaboradors', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('apellido')
                ->comment('Ruta de la foto en storage/app/public/colaboradores');
        });
    }

    public function down(): void
    {
        Schema::table('colaboradors', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
