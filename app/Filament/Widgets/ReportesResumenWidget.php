<?php

namespace App\Filament\Widgets;

use App\Models\Reporte;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class ReportesResumenWidget extends Widget
{
    protected static ?int    $sort            = 1;
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';
    protected static string  $view = 'filament.widgets.reportes-resumen-interactivo';

    public ?string $filtroActivo = null;

    public function seleccionarFiltro(string $estado): void
    {
        $this->filtroActivo = ($this->filtroActivo === $estado || $estado === 'all')
            ? null
            : $estado;

        // Invalidar caché cuando cambia el filtro
        Cache::forget('reportes_resumen');

        $this->dispatch('filtroReporteSeleccionado', estado: $this->filtroActivo);
    }

    public function getData(): array
    {
        $hoy = Carbon::today()->toDateString();

        // ✅ Caché de 60 segundos para los conteos
        $stats = Cache::remember('reportes_resumen', 60, function () use ($hoy) {
            return [
                'total'      => Reporte::count(),
                'hoyCount'   => Reporte::whereDate('created_at', $hoy)->count(),
                'criticos'   => Reporte::whereHas('prioridad', fn ($q) =>
                                    $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
                                )->whereHas('estado', fn ($q) =>
                                    $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
                                )->count(),
                'pendientes' => Reporte::whereHas('estado', fn ($q) =>
                                    $q->where('descripcion', 'Pendiente'))->count(),
                'enProceso'  => Reporte::whereHas('estado', fn ($q) =>
                                    $q->whereIn('descripcion', ['En proceso', 'Asignado']))->count(),
                'finalizados'=> Reporte::whereHas('estado', fn ($q) =>
                                    $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count(),
            ];
        });

        $stats['pctResuelto']  = $stats['total'] > 0
            ? round(($stats['finalizados'] / $stats['total']) * 100)
            : 0;
        $stats['filtroActivo'] = $this->filtroActivo;

        return $stats;
    }
}
