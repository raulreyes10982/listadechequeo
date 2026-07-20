<?php

namespace App\Filament\Widgets;

use App\Models\CategoriaReporte;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Reporte;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

/**
 * Widget con las 3 donas juntas en una sola fila:
 *  - Distribución por estado
 *  - Distribución por prioridad
 *  - Distribución por categoría
 *
 * Reacciona al filtro de período del widget de tabla (ReportesPendientesWidget)
 * y al filtro de estado de las tarjetas (ReportesResumenWidget).
 */
class ReportesDonaWidget extends Widget
{
    protected static ?int    $sort            = 3;
    protected static ?string $pollingInterval = '120s';
    protected int | string | array $columnSpan = 'full';
    protected static string  $view = 'filament.widgets.reportes-dona';

    // Período activo (sincronizado con la tabla)
    public string  $periodo    = 'todos';
    public ?string $fechaDesde = null;
    public ?string $fechaHasta = null;

    #[On('filtroReporteSeleccionado')]
    public function actualizarFiltroEstado(?string $estado): void
    {
        // Las donas no filtran por estado — muestran siempre la distribución global
        // del período seleccionado. El filtro de estado es solo para la tabla.
    }

    #[On('periodoCambiado')]
    public function actualizarPeriodo(string $periodo, ?string $desde = null, ?string $hasta = null): void
    {
        $this->periodo    = $periodo;
        $this->fechaDesde = $desde;
        $this->fechaHasta = $hasta;
    }

    public function getData(): array
    {
        $query = Reporte::query();

        // Aplicar filtro de período
        match ($this->periodo) {
            'ayer'        => $query->whereDate('created_at', Carbon::yesterday()),
            'semanal'     => $query->whereBetween('created_at', [
                                Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'mensual'     => $query->whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year),
            'trimestral'  => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(3), Carbon::now()]),
            'semestral'   => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(6), Carbon::now()]),
            'anual'       => $query->whereYear('created_at', Carbon::now()->year),
            'personalizado' => $query->when($this->fechaDesde, fn ($q) =>
                                    $q->whereDate('created_at', '>=', $this->fechaDesde)
                                )->when($this->fechaHasta, fn ($q) =>
                                    $q->whereDate('created_at', '<=', $this->fechaHasta)),
            default       => null,
        };

        $total = (clone $query)->count();

        // ── Por estado ────────────────────────────────────────────────────
        $estados = Estado::all()->map(function ($e) use ($query, $total) {
            $count = (clone $query)->where('estado_id', $e->id)->count();
            return [
                'label' => $e->descripcion,
                'count' => $count,
                'pct'   => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                'color' => match (strtolower($e->descripcion)) {
                    'pendiente'  => '#f59e0b',
                    'en proceso' => '#3b82f6',
                    'finalizado', 'cerrado', 'resuelto' => '#22c55e',
                    'cancelado'  => '#9ca3af',
                    default      => '#6366f1',
                },
            ];
        })->filter(fn ($e) => $e['count'] > 0)->values();

        // ── Por prioridad ─────────────────────────────────────────────────
        $prioridades = Prioridad::all()->map(function ($p) use ($query, $total) {
            $count = (clone $query)->where('prioridad_id', $p->id)->count();
            return [
                'label' => $p->descripcion,
                'count' => $count,
                'pct'   => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                'color' => match (strtolower($p->descripcion)) {
                    'alta', 'urgente', 'crítica', 'critica' => '#ef4444',
                    'media'  => '#f59e0b',
                    'baja'   => '#22c55e',
                    default  => '#6366f1',
                },
            ];
        })->filter(fn ($p) => $p['count'] > 0)->values();

        // ── Por categoría ─────────────────────────────────────────────────
        $coloresCat = ['#2a78d6', '#4a3aa7', '#1baf7a', '#e87ba4', '#eda100', '#6366f1', '#ef4444'];

        $categorias = CategoriaReporte::all()->values()->map(function ($c, $i) use ($query, $total, $coloresCat) {
            $count = (clone $query)->where('categoria_reporte_id', $c->id)->count();
            return [
                'label' => $c->descripcion,
                'count' => $count,
                'pct'   => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                'color' => $coloresCat[$i % count($coloresCat)],
            ];
        })->filter(fn ($c) => $c['count'] > 0)->values();

        return [
            'total'      => $total,
            'periodo'    => $this->periodo,
            'estados'    => $estados,
            'prioridades'=> $prioridades,
            'categorias' => $categorias,
        ];
    }
}
