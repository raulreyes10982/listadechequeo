<?php

namespace App\Filament\Widgets;

use App\Models\Reporte;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class ReportesPorMesChart extends ChartWidget
{
    protected static ?int    $sort       = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight  = '260px';

    // ✅ Filtro reactivo recibido desde ReportesResumenWidget
    public ?string $estadoFiltro = null;

    public function getHeading(): string
    {
        return match ($this->estadoFiltro) {
            'critico'   => '📊 Reportes críticos — últimos 6 meses',
            'pendiente' => '📊 Reportes pendientes — últimos 6 meses',
            'en_proceso'=> '📊 Reportes en proceso — últimos 6 meses',
            'finalizado'=> '📊 Reportes finalizados — últimos 6 meses',
            default     => '📊 Todos los reportes — últimos 6 meses',
        };
    }

    // ✅ Escucha el evento del widget de tarjetas
    #[On('filtroReporteSeleccionado')]
    public function actualizarFiltro(?string $estado): void
    {
        $this->estadoFiltro = ($estado === 'all' || $estado === null) ? null : $estado;
    }

    protected function getData(): array
    {
        $meses    = [];
        $serie1   = []; // Principal (resueltos, pendientes, etc.)
        $serie2   = []; // Abiertos (solo cuando filtro = null)

        for ($i = 5; $i >= 0; $i--) {
            $fecha  = Carbon::now()->subMonths($i);
            $inicio = $fecha->copy()->startOfMonth();
            $fin    = $fecha->copy()->endOfMonth();

            $meses[] = $fecha->translatedFormat('M Y');

            $base = Reporte::whereBetween('created_at', [$inicio, $fin]);

            switch ($this->estadoFiltro) {
                case 'critico':
                    $serie1[] = (clone $base)->whereHas('prioridad', fn ($q) =>
                        $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
                    )->whereHas('estado', fn ($q) =>
                        $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
                    )->count();
                    break;
                case 'pendiente':
                    $serie1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->where('descripcion', 'Pendiente'))->count();
                    break;
                case 'en_proceso':
                    $serie1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->where('descripcion', 'En proceso'))->count();
                    break;
                case 'finalizado':
                    $serie1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count();
                    break;
                default:
                    // Sin filtro: resueltos y abiertos apilados
                    $resueltos = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count();
                    $total = (clone $base)->count();
                    $serie1[] = $resueltos;
                    $serie2[] = $total - $resueltos;
                    break;
            }
        }

        // Con filtro: una sola barra de color
        if ($this->estadoFiltro) {
            $color = match ($this->estadoFiltro) {
                'critico'   => '#ef4444',
                'pendiente' => '#f59e0b',
                'en_proceso'=> '#3b82f6',
                'finalizado'=> '#22c55e',
                default     => '#6366f1',
            };
            $label = match ($this->estadoFiltro) {
                'critico'   => 'Críticos',
                'pendiente' => 'Pendientes',
                'en_proceso'=> 'En proceso',
                'finalizado'=> 'Finalizados',
                default     => 'Reportes',
            };

            return [
                'datasets' => [[
                    'label'           => $label,
                    'data'            => $serie1,
                    'backgroundColor' => $color,
                    'borderRadius'    => 4,
                ]],
                'labels' => $meses,
            ];
        }

        // Sin filtro: doble barra apilada
        return [
            'datasets' => [
                [
                    'label'           => 'Resueltos',
                    'data'            => $serie1,
                    'backgroundColor' => '#22c55e',
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Abiertos',
                    'data'            => $serie2,
                    'backgroundColor' => '#ef4444',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $meses,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true],
            ],
            'animation' => ['duration' => 400],
        ];
    }
}
