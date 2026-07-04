<?php

namespace App\Console\Commands;

use App\Models\Novedad;
use App\Models\RegistrarTurno;
use App\Models\TipoNovedad;
use App\Models\VerificacionTurno;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CerrarTurnosVencidos extends Command
{
    /*
    |--------------------------------------------------------------------------
    | DESCRIPCIÓN
    |--------------------------------------------------------------------------
    | Este comando se ejecuta automáticamente cada hora vía el scheduler.
    | Busca verificaciones de SALIDA en estado 'pendiente' cuyo turno ya
    | terminó + el tiempo de gracia configurado.
    |
    | Si no hay registro de salida del guardia:
    |   1. Marca la verificación como 'vencido'
    |   2. Registra el motivo y la hora del cierre automático
    |   3. Crea una Novedad automática para que el supervisor lo vea
    |
    | NO marca como 'verificado' — es un estado diferente que indica
    | anomalía y aparece en rojo en el dashboard.
    |--------------------------------------------------------------------------
    */

    protected $signature = 'turnos:cerrar-vencidos
        {--gracia=60 : Minutos de gracia después de hora_fin antes de marcar como vencido}
        {--dry-run   : Simula sin guardar nada en la BD}';

    protected $description = 'Cierra automáticamente verificaciones de salida pendientes cuyo turno ya venció';

    public function handle(): int
    {
        $gracia = (int) $this->option('gracia');
        $dryRun = $this->option('dry-run');
        $ahora  = Carbon::now();

        $this->info("🕐 Ejecutando a: {$ahora->format('d/m/Y H:i:s')}");
        $this->info("⏱ Tiempo de gracia configurado: {$gracia} minutos");

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: no se guardará nada en la BD');
        }

        // ── Buscar verificaciones de SALIDA pendientes con turno vencido ──

        $verificaciones = VerificacionTurno::query()
            ->where('tipo', 'salida')
            ->where('estado', 'pendiente')
            ->whereHas('turno', function ($q) use ($ahora, $gracia) {
                // El turno es de HOY o AYER (turnos nocturnos)
                $q->where(function ($inner) use ($ahora, $gracia) {
                    // Turno normal: hora_fin ya pasó + gracia
                    $inner->whereDate('fecha', $ahora->toDateString())
                        ->whereColumn('hora_inicio', '<=', 'hora_fin')
                        ->whereRaw("ADDTIME(hora_fin, SEC_TO_TIME(? * 60)) <= ?", [
                            $gracia,
                            $ahora->toTimeString(),
                        ]);
                })->orWhere(function ($inner) use ($ahora, $gracia) {
                    // Turno del día anterior que ya venció (turno nocturno)
                    $inner->whereDate('fecha', $ahora->copy()->subDay()->toDateString())
                        ->whereColumn('hora_inicio', '>', 'hora_fin')
                        ->whereRaw("ADDTIME(hora_fin, SEC_TO_TIME(? * 60)) <= ?", [
                            $gracia,
                            $ahora->toTimeString(),
                        ]);
                });
            })
            ->with(['turno.colaborador', 'turno.puestoSeguridad'])
            ->get();

        if ($verificaciones->isEmpty()) {
            $this->info('✅ No hay turnos vencidos sin salida registrada.');
            return self::SUCCESS;
        }

        $this->info("⚠️  {$verificaciones->count()} turno(s) vencido(s) encontrado(s):");

        // Resolver tipo de novedad automática (buscar o usar null si no existe)
        $tipoNovedad = TipoNovedad::where('descripcion', 'like', '%automát%')
            ->orWhere('descripcion', 'like', '%sistema%')
            ->first();

        $procesados = 0;
        $errores    = 0;

        foreach ($verificaciones as $verificacion) {
            $turno       = $verificacion->turno;
            $colaborador = $turno->colaborador;
            $puesto      = $turno->puestoSeguridad;

            $nombreGuardia = trim(($colaborador->nombre ?? '') . ' ' . ($colaborador->apellido ?? ''));
            $nombrePuesto  = $puesto->puesto ?? 'Sin puesto';
            $codigo        = $puesto->codigo ?? '—';
            $horaFin       = Carbon::parse($turno->hora_fin)->format('H:i');
            $fecha         = Carbon::parse($turno->fecha)->format('d/m/Y');

            $motivo = "Cierre automático — turno finalizó a las {$horaFin} el {$fecha}. " .
                      "Guardia {$nombreGuardia} no registró salida en el puesto {$codigo} {$nombrePuesto}.";

            $this->line("   → [{$codigo}] {$nombreGuardia} | Turno {$fecha} hasta {$horaFin}");

            if ($dryRun) {
                $procesados++;
                continue;
            }

            try {
                DB::transaction(function () use ($verificacion, $turno, $tipoNovedad, $motivo, $ahora, $nombreGuardia, $codigo, $nombrePuesto) {

                    // 1. Marcar la verificación como 'vencido'
                    $verificacion->update([
                        'estado'                    => 'vencido',
                        'observacion'               => $motivo,
                        'cierre_automatico_motivo'  => $motivo,
                        'cierre_automatico_en'      => $ahora,
                    ]);

                    // 2. Crear novedad automática para el supervisor
                    if ($tipoNovedad) {
                        Novedad::create([
                            'tipo_novedad_id' => $tipoNovedad->id,
                            'descripcion'     => $motivo,
                            'subidopor'       => 'Sistema automático',
                            'fecha'           => $ahora->toDateString(),
                            'hora'            => $ahora->toTimeString(),
                        ]);
                    }
                });

                $procesados++;

            } catch (\Throwable $e) {
                $errores++;
                $this->error("   ✗ Error procesando verificación #{$verificacion->id}: {$e->getMessage()}");
                Log::error("CerrarTurnosVencidos — error en verificación #{$verificacion->id}", [
                    'error'           => $e->getMessage(),
                    'verificacion_id' => $verificacion->id,
                    'turno_id'        => $turno->id,
                ]);
            }
        }

        $this->info('');
        $this->info("✅ Procesados: {$procesados} | ❌ Errores: {$errores}");

        Log::info("CerrarTurnosVencidos completado", [
            'procesados' => $procesados,
            'errores'    => $errores,
            'gracia_min' => $gracia,
            'ejecutado'  => $ahora->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}
