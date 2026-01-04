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
        // Verificar si la columna ya existe antes de agregarla
        if (!Schema::hasColumn('puesto_seguridads', 'qr_generado_en')) {
            Schema::table('puesto_seguridads', function (Blueprint $table) {
                $table->timestamp('qr_generado_en')
                    ->nullable()
                    ->after('qr_expira')
                    ->comment('Fecha y hora en que se generó el token QR');
            });
            
            // Opcional: Establecer fecha para registros existentes
            DB::table('puesto_seguridads')
                ->whereNotNull('qr_token')
                ->update(['qr_generado_en' => DB::raw('updated_at')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('puesto_seguridads', 'qr_generado_en')) {
            Schema::table('puesto_seguridads', function (Blueprint $table) {
                $table->dropColumn('qr_generado_en');
            });
        }
    }
};