<?php

namespace App\Filament\Widgets;

use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SecurityStatsWidget extends BaseWidget
{
    protected static ?int    $sort            = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $now = Carbon::now();

        // ── 1. Guardias con turno HOY ─────────────────────────────────────
        $turnosHoy  = RegistrarTurno::whereDate('fecha', $hoy)->count();

        $conIngreso = RegistrarTurno::whereDate('fecha', $hoy)
            ->whereHas('verificaciones', fn ($q) =>
                $q->where('tipo', 'ingreso')->where('estado', 'verificado')
            )->count();

        // ── 2. Puestos sin cobertura ahora mismo ──────────────────────────
        $sinCobertura = RegistrarTurno::whereDate('fecha', $hoy)
            ->whereTime('hora_inicio', '<=', $now->toTimeString())
            ->whereTime('hora_fin',    '>=', $now->toTimeString())
            ->whereDoesntHave('verificaciones', fn ($q) =>
                $q->where('tipo', 'ingreso')->where('estado', 'verificado')
            )->count();

        // ── 3. Turnos vencidos sin salida (cierre automático hoy) ─────────
        $vencidosSinSalida = RegistrarTurno::whereDate('fecha', $hoy)
            ->whereHas('verificaciones', fn ($q) =>
                $q->where('tipo', 'salida')->where('estado', 'vencido')
            )->count();

        return [
            Stat::make('Guardias programados hoy', $turnosHoy)
                ->description("{$conIngreso} con ingreso verificado")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($conIngreso === $turnosHoy && $turnosHoy > 0 ? 'success' : 'warning')
                ->chart([$turnosHoy, $conIngreso]),

            Stat::make('Puestos sin cobertura', $sinCobertura)
                ->description('Turnos activos sin escaneo de ingreso')
                ->descriptionIcon($sinCobertura > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-shield-check')
                ->color($sinCobertura > 0 ? 'danger' : 'success'),

            Stat::make('Sin salida registrada', $vencidosSinSalida)
                ->description('Turnos cerrados automáticamente hoy')
                ->descriptionIcon($vencidosSinSalida > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($vencidosSinSalida > 0 ? 'warning' : 'success'),
        ];
    }
}
