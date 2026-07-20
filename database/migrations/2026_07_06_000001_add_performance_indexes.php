<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
    |--------------------------------------------------------------------------
    | Índices de rendimiento para las queries más frecuentes del dashboard
    |--------------------------------------------------------------------------
    | Sin índices, cada query del dashboard hace un full table scan.
    | Con índices, MySQL encuentra los registros en microsegundos.
    |
    | Impacto estimado:
    |   registrar_turnos   → de 800ms a <10ms con 10k registros
    |   verificacion_turnos → de 600ms a <5ms
    |   reportes           → de 400ms a <8ms
    |--------------------------------------------------------------------------
    */

    public function up(): void
    {
        // ── registrar_turnos ──────────────────────────────────────────────
        Schema::table('registrar_turnos', function (Blueprint $table) {
            // Dashboard: WHERE fecha = today AND puesto_seguridad_id = X
            $table->index(['fecha', 'puesto_seguridad_id'], 'idx_turnos_fecha_puesto');
            // Dashboard: WHERE fecha = today AND colaborador_id = X
            $table->index(['fecha', 'colaborador_id'], 'idx_turnos_fecha_colaborador');
            // Scheduler cierre automático: WHERE hora_fin <= now
            $table->index(['hora_inicio', 'hora_fin'], 'idx_turnos_horas');
        });

        // ── verificacion_turnos ───────────────────────────────────────────
        Schema::table('verificacion_turnos', function (Blueprint $table) {
            // Dashboard: WHERE tipo = 'ingreso' AND estado = 'verificado'
            $table->index(['tipo', 'estado'], 'idx_verif_tipo_estado');
            // QR Scanner: WHERE registrar_turno_id = X AND tipo = 'salida'
            $table->index(['registrar_turno_id', 'tipo'], 'idx_verif_turno_tipo');
            // Historial: WHERE estado = 'vencido'
            $table->index('estado', 'idx_verif_estado');
        });

        // ── reportes ──────────────────────────────────────────────────────
        Schema::table('reportes', function (Blueprint $table) {
            // Dashboard: WHERE created_at BETWEEN x AND y
            $table->index('created_at', 'idx_reportes_created');
            // Filtros: WHERE estado_id = X AND prioridad_id = Y
            $table->index(['estado_id', 'prioridad_id'], 'idx_reportes_estado_prio');
            // Donas: WHERE categoria_reporte_id = X
            $table->index('categoria_reporte_id', 'idx_reportes_categoria');
        });

        // ── colaboradors ──────────────────────────────────────────────────
        Schema::table('colaboradors', function (Blueprint $table) {
            // UserResource: buscar por correo
            $table->index(['correo_corporativo', 'correo_personal'], 'idx_colab_correos');
            // resolverColaborador: WHERE user_id = X
            $table->index('user_id', 'idx_colab_user');
        });

        // ── permisos ──────────────────────────────────────────────────────
        Schema::table('permisos', function (Blueprint $table) {
            // Dashboard permisos: WHERE fecha_fin_trabajo BETWEEN x AND y
            $table->index('fecha_fin_trabajo', 'idx_permisos_fecha_fin');
        });
    }

    public function down(): void
    {
        Schema::table('registrar_turnos', function (Blueprint $table) {
            $table->dropIndex('idx_turnos_fecha_puesto');
            $table->dropIndex('idx_turnos_fecha_colaborador');
            $table->dropIndex('idx_turnos_horas');
        });

        Schema::table('verificacion_turnos', function (Blueprint $table) {
            $table->dropIndex('idx_verif_tipo_estado');
            $table->dropIndex('idx_verif_turno_tipo');
            $table->dropIndex('idx_verif_estado');
        });

        Schema::table('reportes', function (Blueprint $table) {
            $table->dropIndex('idx_reportes_created');
            $table->dropIndex('idx_reportes_estado_prio');
            $table->dropIndex('idx_reportes_categoria');
        });

        Schema::table('colaboradors', function (Blueprint $table) {
            $table->dropIndex('idx_colab_correos');
            $table->dropIndex('idx_colab_user');
        });

        Schema::table('permisos', function (Blueprint $table) {
            $table->dropIndex('idx_permisos_fecha_fin');
        });
    }
};
