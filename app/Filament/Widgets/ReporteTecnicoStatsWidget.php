<?php

namespace App\Filament\Widgets;

use App\Models\ReporteTecnico;
use App\Models\EstadoReporte;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReporteTecnicoStatsWidget extends BaseWidget
{
    protected static ?int    $sort            = 6;
    protected static ?string $pollingInterval = '60s';
    protected ?string        $heading         = 'Reportes Técnicos de Equipos';

    protected function getStats(): array
    {
        $hoy  = Carbon::today();
        $ayer = Carbon::yesterday();

        // IDs de estados "finalizados" — busca por nombre flexible
        $idsFinalizados = EstadoReporte::whereIn('nombre', [
            'Finalizado', 'Cerrado', 'Resuelto', 'Completado',
        ])->pluck('id');

        $idsPendientes = EstadoReporte::whereIn('nombre', [
            'Pendiente', 'Sin atender', 'Nuevo',
        ])->pluck('id');

        // ── 1. Total de reportes técnicos ─────────────────────────────────
        $total = ReporteTecnico::count();

        // ── 2. Reportes creados ayer ──────────────────────────────────────
        $ayer_count = ReporteTecnico::whereDate('fecha', $ayer)->count();

        // ── 3. Reportes del día de hoy ────────────────────────────────────
        $hoy_count = ReporteTecnico::whereDate('fecha', $hoy)->count();

        // ── 4. No atendidos (último estado = Pendiente) ───────────────────
        $noAtendidos = ReporteTecnico::whereHas('ultimoEstado', fn ($q) =>
            $q->whereHas('estadoReporte', fn ($q) =>
                $q->whereIn('id', $idsPendientes)
            )
        )->count();

        // ── 5. Finalizados ────────────────────────────────────────────────
        $finalizados = ReporteTecnico::whereHas('ultimoEstado', fn ($q) =>
            $q->whereHas('estadoReporte', fn ($q) =>
                $q->whereIn('id', $idsFinalizados)
            )
        )->count();

        // ── 6. En proceso ─────────────────────────────────────────────────
        $enProceso = $total - $noAtendidos - $finalizados;

        // ── Porcentaje de resolución ──────────────────────────────────────
        $pctResuelto = $total > 0
            ? round(($finalizados / $total) * 100)
            : 0;

        return [
            Stat::make('Total reportes técnicos', $total)
                ->description("Hoy: {$hoy_count} nuevo(s)")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info')
                ->chart([$total]),

            Stat::make('Reportes de ayer', $ayer_count)
                ->description(Carbon::yesterday()->translatedFormat('l d/m/Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color($ayer_count > 0 ? 'warning' : 'gray'),

            Stat::make('Sin atender', $noAtendidos)
                ->description('Último estado: Pendiente')
                ->descriptionIcon(
                    $noAtendidos > 0
                        ? 'heroicon-m-exclamation-circle'
                        : 'heroicon-m-check-circle'
                )
                ->color($noAtendidos > 0 ? 'danger' : 'success'),

            Stat::make('En proceso', $enProceso > 0 ? $enProceso : 0)
                ->description('Trabajando en ello')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($enProceso > 0 ? 'warning' : 'gray'),

            Stat::make('Finalizados', $finalizados)
                ->description("{$pctResuelto}% del total resuelto")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($pctResuelto >= 80 ? 'success' : ($pctResuelto >= 50 ? 'warning' : 'danger')),
        ];
    }
}
