<?php

namespace App\Filament\Widgets;

use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class SecurityStatsWidget extends BaseWidget
{
    protected static ?int    $sort            = 1;
    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $hoy = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        $data = Cache::remember("security_stats_{$hoy}", 60, function () use ($hoy, $now) {
            return [
                'turnosHoy' => RegistrarTurno::whereDate('fecha', $hoy)->count(),

                'conIngreso' => RegistrarTurno::whereDate('fecha', $hoy)
                    ->whereHas('verificaciones', fn ($q) =>
                        $q->where('tipo', 'ingreso')->where('estado', 'verificado')
                    )->count(),

                'sinCobertura' => RegistrarTurno::whereDate('fecha', $hoy)
                    ->whereTime('hora_inicio', '<=', $now)
                    ->whereTime('hora_fin',    '>=', $now)
                    ->whereDoesntHave('verificaciones', fn ($q) =>
                        $q->where('tipo', 'ingreso')->where('estado', 'verificado')
                    )->count(),

                'vencidosSinSalida' => RegistrarTurno::whereDate('fecha', $hoy)
                    ->whereHas('verificaciones', fn ($q) =>
                        $q->where('tipo', 'salida')->where('estado', 'vencido')
                    )->count(),
            ];
        });

        return [
            Stat::make('Guardias programados hoy', $data['turnosHoy'])
                ->description("{$data['conIngreso']} con ingreso verificado")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($data['conIngreso'] === $data['turnosHoy'] && $data['turnosHoy'] > 0
                    ? 'success' : 'warning')
                ->chart([$data['turnosHoy'], $data['conIngreso']]),

            Stat::make('Puestos sin cobertura', $data['sinCobertura'])
                ->description('Turnos activos sin escaneo de ingreso')
                ->descriptionIcon($data['sinCobertura'] > 0
                    ? 'heroicon-m-exclamation-circle'
                    : 'heroicon-m-shield-check')
                ->color($data['sinCobertura'] > 0 ? 'danger' : 'success'),

            Stat::make('Sin salida registrada', $data['vencidosSinSalida'])
                ->description('Turnos cerrados automáticamente hoy')
                ->descriptionIcon($data['vencidosSinSalida'] > 0
                    ? 'heroicon-m-exclamation-triangle'
                    : 'heroicon-m-check-circle')
                ->color($data['vencidosSinSalida'] > 0 ? 'warning' : 'success'),
        ];
    }
}
