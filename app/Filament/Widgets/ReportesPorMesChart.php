<?php

namespace App\Filament\Widgets;

use App\Models\Reporte;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class ReportesPorMesChart extends ChartWidget
{
    protected static ?int    $sort      = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '260px';

    public ?string $estadoFiltro = null;
    public string  $periodo      = 'todos';
    public ?string $fechaDesde   = null;
    public ?string $fechaHasta   = null;

    // ✅ Escucha filtro de estado desde las tarjetas
    #[On('filtroReporteSeleccionado')]
    public function actualizarFiltroEstado(mixed $estado = null): void
    {
        $this->estadoFiltro = (is_null($estado) || $estado === 'all') ? null : (string) $estado;
    }

    // ✅ Escucha cambio de período desde la tabla
    #[On('periodoCambiado')]
    public function actualizarPeriodo(string $periodo, ?string $desde = null, ?string $hasta = null): void
    {
        $this->periodo    = $periodo;
        $this->fechaDesde = $desde;
        $this->fechaHasta = $hasta;
    }

    public function getHeading(): string
    {
        $p = match ($this->periodo) {
            'ayer'        => 'Ayer',
            'semanal'     => 'Esta semana',
            'mensual'     => 'Este mes',
            'trimestral'  => 'Último trimestre',
            'semestral'   => 'Último semestre',
            'anual'       => 'Este año',
            'personalizado' => 'Período personalizado',
            default       => 'Últimos 6 meses',
        };

        $e = match ($this->estadoFiltro) {
            'critico'    => ' · Críticos',
            'pendiente'  => ' · Pendientes',
            'en_proceso' => ' · En proceso',
            'finalizado' => ' · Finalizados',
            default      => '',
        };

        return "📊 Reportes registrados — {$p}{$e}";
    }

    protected function getData(): array
    {
        // ── Determinar rango según período ────────────────────────────────
        $rangos = $this->getRangos();

        $series1 = [];
        $series2 = [];
        $labels  = [];

        foreach ($rangos as $rango) {
            $labels[]  = $rango['label'];
            $base = Reporte::whereBetween('created_at', [$rango['inicio'], $rango['fin']]);

            switch ($this->estadoFiltro) {
                case 'critico':
                    $series1[] = (clone $base)->whereHas('prioridad', fn ($q) =>
                        $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
                    )->whereHas('estado', fn ($q) =>
                        $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
                    )->count();
                    break;
                case 'pendiente':
                    $series1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->where('descripcion', 'Pendiente'))->count();
                    break;
                case 'en_proceso':
                    $series1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->whereIn('descripcion', ['En proceso', 'Asignado']))->count();
                    break;
                case 'finalizado':
                    $series1[] = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count();
                    break;
                default:
                    $resueltos = (clone $base)->whereHas('estado', fn ($q) =>
                        $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto']))->count();
                    $total = (clone $base)->count();
                    $series1[] = $resueltos;
                    $series2[] = $total - $resueltos;
                    break;
            }
        }

        if ($this->estadoFiltro) {
            $color = match ($this->estadoFiltro) {
                'critico'    => '#ef4444',
                'pendiente'  => '#f59e0b',
                'en_proceso' => '#3b82f6',
                'finalizado' => '#22c55e',
                default      => '#6366f1',
            };
            $label = match ($this->estadoFiltro) {
                'critico'    => 'Críticos',
                'pendiente'  => 'Pendientes',
                'en_proceso' => 'En proceso',
                'finalizado' => 'Finalizados',
                default      => 'Reportes',
            };

            return [
                'datasets' => [[
                    'label'           => $label,
                    'data'            => $series1,
                    'backgroundColor' => $color,
                    'borderRadius'    => 4,
                ]],
                'labels' => $labels,
            ];
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Resueltos',
                    'data'            => $series1,
                    'backgroundColor' => '#22c55e',
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Abiertos',
                    'data'            => $series2,
                    'backgroundColor' => '#ef4444',
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Generar los rangos de fechas según el período seleccionado
    |--------------------------------------------------------------------------
    */
    private function getRangos(): array
    {
        switch ($this->periodo) {
            case 'ayer':
                // Últimas 7 horas del ayer (por hora)
                return collect(range(6, 0))->map(fn ($h) => [
                    'label'  => Carbon::yesterday()->subHours($h)->format('H:00'),
                    'inicio' => Carbon::yesterday()->subHours($h)->startOfHour(),
                    'fin'    => Carbon::yesterday()->subHours($h)->endOfHour(),
                ])->values()->toArray();

            case 'semanal':
                // Días de la semana actual
                return collect(range(6, 0))->map(fn ($d) => [
                    'label'  => Carbon::now()->subDays($d)->translatedFormat('D d/M'),
                    'inicio' => Carbon::now()->subDays($d)->startOfDay(),
                    'fin'    => Carbon::now()->subDays($d)->endOfDay(),
                ])->values()->toArray();

            case 'mensual':
                // Semanas del mes actual
                return collect(range(3, 0))->map(fn ($w) => [
                    'label'  => 'Sem ' . ($w + 1),
                    'inicio' => Carbon::now()->startOfMonth()->addWeeks($w),
                    'fin'    => Carbon::now()->startOfMonth()->addWeeks($w + 1)->subSecond(),
                ])->values()->toArray();

            case 'trimestral':
                // Últimos 3 meses
                return collect(range(2, 0))->map(fn ($m) => [
                    'label'  => Carbon::now()->subMonths($m)->translatedFormat('M Y'),
                    'inicio' => Carbon::now()->subMonths($m)->startOfMonth(),
                    'fin'    => Carbon::now()->subMonths($m)->endOfMonth(),
                ])->values()->toArray();

            case 'semestral':
                // Últimos 6 meses
                return collect(range(5, 0))->map(fn ($m) => [
                    'label'  => Carbon::now()->subMonths($m)->translatedFormat('M Y'),
                    'inicio' => Carbon::now()->subMonths($m)->startOfMonth(),
                    'fin'    => Carbon::now()->subMonths($m)->endOfMonth(),
                ])->values()->toArray();

            case 'anual':
                // Meses del año actual
                return collect(range(0, 11))->map(fn ($m) => [
                    'label'  => Carbon::now()->startOfYear()->addMonths($m)->translatedFormat('M'),
                    'inicio' => Carbon::now()->startOfYear()->addMonths($m)->startOfMonth(),
                    'fin'    => Carbon::now()->startOfYear()->addMonths($m)->endOfMonth(),
                ])->values()->toArray();

            case 'personalizado':
                if ($this->fechaDesde && $this->fechaHasta) {
                    $desde = Carbon::parse($this->fechaDesde);
                    $hasta = Carbon::parse($this->fechaHasta);
                    $dias  = $desde->diffInDays($hasta);

                    if ($dias <= 31) {
                        // Por día
                        return collect(range(0, $dias))->map(fn ($d) => [
                            'label'  => $desde->copy()->addDays($d)->format('d/M'),
                            'inicio' => $desde->copy()->addDays($d)->startOfDay(),
                            'fin'    => $desde->copy()->addDays($d)->endOfDay(),
                        ])->values()->toArray();
                    } else {
                        // Por semana
                        $semanas = ceil($dias / 7);
                        return collect(range(0, $semanas - 1))->map(fn ($w) => [
                            'label'  => $desde->copy()->addWeeks($w)->format('d/M'),
                            'inicio' => $desde->copy()->addWeeks($w),
                            'fin'    => $desde->copy()->addWeeks($w + 1)->subSecond(),
                        ])->values()->toArray();
                    }
                }
                // Fallback a 6 meses
                return $this->getMeses(6);

            default:
                // Últimos 6 meses
                return $this->getMeses(6);
        }
    }

    private function getMeses(int $n): array
    {
        return collect(range($n - 1, 0))->map(fn ($m) => [
            'label'  => Carbon::now()->subMonths($m)->translatedFormat('M Y'),
            'inicio' => Carbon::now()->subMonths($m)->startOfMonth(),
            'fin'    => Carbon::now()->subMonths($m)->endOfMonth(),
        ])->values()->toArray();
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
