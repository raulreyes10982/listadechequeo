<?php

namespace App\Filament\Widgets;

use App\Models\Reporte;
use App\Models\Estado;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class ReportesResumenWidget extends Widget
{
    protected static ?int    $sort            = 1;
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.reportes-resumen-interactivo';

    // ✅ Estado seleccionado — null = todos, 'pendiente'/'en proceso'/'finalizado'
    public ?string $filtroActivo = null;

    public function seleccionarFiltro(string $estado): void
    {
        // Si ya está seleccionado, deseleccionar (toggle)
        $this->filtroActivo = $this->filtroActivo === $estado ? null : $estado;

        // Notificar a la gráfica y a la tabla
        $this->dispatch('filtroReporteSeleccionado', estado: $this->filtroActivo);
    }

    public function getData(): array
    {
        $hoy = Carbon::today();
        $now = Carbon::now();

        $total       = Reporte::count();
        $hoyCount    = Reporte::whereDate('created_at', $hoy)->count();

        $criticos = Reporte::whereHas('prioridad', fn ($q) =>
                $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
            )
            ->whereHas('estado', fn ($q) =>
                $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
            )->count();

        $pendientes  = Reporte::whereHas('estado', fn ($q) =>
            $q->where('descripcion', 'Pendiente'))->count();

        $enProceso   = Reporte::whereHas('estado', fn ($q) =>
            $q->where('descripcion', 'En proceso'))->count();

        $finalizados = Reporte::whereHas('estado', fn ($q) =>
            $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count();

        $pctResuelto = $total > 0 ? round(($finalizados / $total) * 100) : 0;

        return [
            'total'        => $total,
            'hoyCount'     => $hoyCount,
            'criticos'     => $criticos,
            'pendientes'   => $pendientes,
            'enProceso'    => $enProceso,
            'finalizados'  => $finalizados,
            'pctResuelto'  => $pctResuelto,
            'filtroActivo' => $this->filtroActivo,
        ];
    }
}
