<?php

namespace App\Filament\Widgets;

use App\Models\ReporteTecnico;
use App\Models\EstadoReporte;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ReporteTecnicoStatsWidget extends BaseWidget
{
    protected static ?int    $sort            = 6;
    protected static ?string $pollingInterval = '60s';
    protected ?string $heading = 'Reportes Técnicos de Equipos';

    protected function getStats(): array
    {
        $hoy  = Carbon::today()->toDateString();
        $ayer = Carbon::yesterday()->toDateString();

        // ✅ Caché de 2 minutos — los técnicos no cambian tan rápido
        return Cache::remember('reporte_tecnico_stats', 120, function () use ($hoy, $ayer) {

            $idsFinalizados = EstadoReporte::whereIn('nombre', [
                'Finalizado', 'Cerrado', 'Resuelto', 'Completado',
            ])->pluck('id');

            $idsPendientes = EstadoReporte::whereIn('nombre', [
                'Pendiente', 'Sin atender', 'Nuevo',
            ])->pluck('id');

            $total      = ReporteTecnico::count();
            $ayerCount  = ReporteTecnico::whereDate('fecha', $ayer)->count();
            $hoyCount   = ReporteTecnico::whereDate('fecha', $hoy)->count();

            $noAtendidos = ReporteTecnico::whereHas('ultimoEstado', fn ($q) =>
                $q->whereHas('estadoReporte', fn ($q) =>
                    $q->whereIn('id', $idsPendientes)
                )
            )->count();

            $finalizados = ReporteTecnico::whereHas('ultimoEstado', fn ($q) =>
                $q->whereHas('estadoReporte', fn ($q) =>
                    $q->whereIn('id', $idsFinalizados)
                )
            )->count();

            $enProceso   = max(0, $total - $noAtendidos - $finalizados);
            $pctResuelto = $total > 0 ? round(($finalizados / $total) * 100) : 0;

            return compact(
                'total', 'ayerCount', 'hoyCount',
                'noAtendidos', 'enProceso', 'finalizados', 'pctResuelto'
            );
        });
    }

    protected function getCards(): array
    {
        $s = $this->getStats();

        return [
            Stat::make('Total reportes técnicos', $s['total'])
                ->description("Hoy: {$s['hoyCount']} nuevo(s)")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),

            Stat::make('Reportes de ayer', $s['ayerCount'])
                ->description(Carbon::yesterday()->translatedFormat('l d/m/Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color($s['ayerCount'] > 0 ? 'warning' : 'gray'),

            Stat::make('Sin atender', $s['noAtendidos'])
                ->description('Último estado: Pendiente')
                ->descriptionIcon($s['noAtendidos'] > 0
                    ? 'heroicon-m-exclamation-circle'
                    : 'heroicon-m-check-circle')
                ->color($s['noAtendidos'] > 0 ? 'danger' : 'success'),

            Stat::make('En proceso', $s['enProceso'])
                ->description('Trabajando en ello')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($s['enProceso'] > 0 ? 'warning' : 'gray'),

            Stat::make('Finalizados', $s['finalizados'])
                ->description("{$s['pctResuelto']}% del total resuelto")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($s['pctResuelto'] >= 80 ? 'success'
                    : ($s['pctResuelto'] >= 50 ? 'warning' : 'danger')),
        ];
    }
}
