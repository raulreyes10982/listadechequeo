<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar 'vencido' al enum de estado en verificacion_turnos
        DB::statement("
            ALTER TABLE verificacion_turnos
            MODIFY COLUMN estado ENUM('pendiente','verificado','cerrado','vencido')
            NOT NULL DEFAULT 'pendiente'
        ");

        // Agregar columna para registrar el motivo del cierre automático
        Schema::table('verificacion_turnos', function (Blueprint $table) {
            $table->string('cierre_automatico_motivo')->nullable()
                ->after('observacion')
                ->comment('Registra el motivo cuando el sistema cierra automáticamente');
            $table->timestamp('cierre_automatico_en')->nullable()
                ->after('cierre_automatico_motivo')
                ->comment('Cuándo ejecutó el sistema el cierre automático');
        });
    }

    public function down(): void
    {
        Schema::table('verificacion_turnos', function (Blueprint $table) {
            $table->dropColumn(['cierre_automatico_motivo', 'cierre_automatico_en']);
        });

        DB::statement("
            ALTER TABLE verificacion_turnos
            MODIFY COLUMN estado ENUM('pendiente','verificado','cerrado')
            NOT NULL DEFAULT 'pendiente'
        ");
    }
};
