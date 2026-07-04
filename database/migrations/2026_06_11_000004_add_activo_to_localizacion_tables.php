<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categoria_locals', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('descripcion');
        });

        Schema::table('nomenclaturas', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('categoria_local_id');
        });

        Schema::table('locals', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('categoria_local_id');
        });
    }

    public function down(): void
    {
        Schema::table('categoria_locals', fn (Blueprint $t) => $t->dropColumn('activo'));
        Schema::table('nomenclaturas',   fn (Blueprint $t) => $t->dropColumn('activo'));
        Schema::table('locals',          fn (Blueprint $t) => $t->dropColumn('activo'));
    }
};
