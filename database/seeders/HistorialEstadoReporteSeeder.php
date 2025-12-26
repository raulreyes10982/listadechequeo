<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HistorialEstadoReporte;
use App\Models\ReporteTecnico;
use App\Models\EstadoReporte;
use Carbon\Carbon;

class HistorialEstadoReporteSeeder extends Seeder
{
    public function run(): void
    {
        $reporte = ReporteTecnico::first();
        $estado = EstadoReporte::first();

        // Validación simple por si no hay datos
        if (!$reporte || !$estado) {
            $this->command->warn('⚠️ No se encontró reporte técnico o estado de reporte para el seeder.');
            return;
        }

        HistorialEstadoReporte::create([
            'cambiado_por' => 'Sistema',
            'fecha' => Carbon::now()->toDateString(),
            'hora' => Carbon::now()->format('H:i:s'),
            'reporte_tecnico_id' => $reporte->id,
            'estado_reporte_id' => $estado->id,
        ]);
    }
}
