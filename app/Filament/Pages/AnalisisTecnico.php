<?php

namespace App\Filament\Pages;

use App\Models\Equipo;
use App\Models\ReporteTecnico;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class AnalisisTecnico extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Análisis Técnico';
    protected static ?string $title           = 'Análisis de Reportes Técnicos';
    protected static ?string $slug            = 'analisis-tecnico';
    protected static ?int    $navigationSort  = 10;

    protected static string $view = 'filament.pages.analisis-tecnico';

    /*
    |--------------------------------------------------------------------------
    | ✅ canAccess() — Filament Shield NO protege páginas personalizadas
    | automáticamente como hace con los Resources. Hay que verificar el
    | permiso manualmente con el nombre que Shield generó: 'page_AnalisisTecnico'
    |--------------------------------------------------------------------------
    */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Los super_admin siempre pasan (Shield los intercepta antes del Gate)
        if ($user->hasRole('super_admin')) {
            return true;
        }

        try {
            return $user->can('page_AnalisisTecnico');
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return false;
        }
    }

    // ID del equipo seleccionado — null = vista grupal (todos)
    public ?int $equipoId = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('equipoId')
                    ->label('Equipo')
                    ->placeholder('🔍 Todos los equipos (vista grupal)')
                    ->options(
                        Equipo::with('tipoEquipo')->get()
                            ->mapWithKeys(fn ($e) => [
                                $e->id => ($e->tipoEquipo->descripcion ?? '') . ' — ' . $e->descripcion,
                            ])
                    )
                    ->searchable()
                    ->live()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function updatedData(): void
    {
        $this->equipoId = $this->data['equipoId'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | VISTA GRUPAL — Ranking de todos los equipos
    |--------------------------------------------------------------------------
    */
    public function getRankingEquipos(): \Illuminate\Support\Collection
    {
        return Equipo::with('tipoEquipo')
            ->get()
            ->map(function ($equipo) {
                $reportes = ReporteTecnico::where('equipo_id', $equipo->id)
                    ->with('ultimoEstado.estadoReporte')
                    ->get();

                $total      = $reportes->count();
                $pendientes = $reportes->filter(fn ($r) =>
                    in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Pendiente', 'Sin atender', 'Nuevo'])
                )->count();
                $finalizados = $reportes->filter(fn ($r) =>
                    in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Finalizado', 'Cerrado', 'Resuelto', 'Completado'])
                )->count();
                $enProceso = $total - $pendientes - $finalizados;

                // Tiempo promedio de resolución (en días) para los finalizados
                $diasPromedio = $reportes
                    ->filter(fn ($r) =>
                        in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Finalizado', 'Cerrado', 'Resuelto', 'Completado'])
                        && $r->ultimoEstado?->created_at
                    )
                    ->map(fn ($r) => Carbon::parse($r->fecha)->diffInDays($r->ultimoEstado->created_at))
                    ->average();

                return (object) [
                    'id'           => $equipo->id,
                    'nombre'       => ($equipo->tipoEquipo->descripcion ?? '') . ' — ' . $equipo->descripcion,
                    'total'        => $total,
                    'pendientes'   => $pendientes,
                    'en_proceso'   => $enProceso,
                    'finalizados'  => $finalizados,
                    'dias_promedio'=> $diasPromedio ? round($diasPromedio, 1) : null,
                    'pct_resuelto' => $total > 0 ? round(($finalizados / $total) * 100) : 0,
                ];
            })
            ->filter(fn ($e) => $e->total > 0) // solo equipos con reportes
            ->sortByDesc('total')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | VISTA INDIVIDUAL — Detalle de un equipo
    |--------------------------------------------------------------------------
    */
    public function getEquipoSeleccionado(): ?Equipo
    {
        if (! $this->equipoId) return null;

        return Equipo::with('tipoEquipo')->find($this->equipoId);
    }

    public function getHistorialEquipo(): \Illuminate\Support\Collection
    {
        if (! $this->equipoId) return collect();

        return ReporteTecnico::where('equipo_id', $this->equipoId)
            ->with(['tipoIntervencion', 'ultimoEstado.estadoReporte', 'historialEstadoReportes.estadoReporte'])
            ->orderByDesc('fecha')
            ->get();
    }

    public function getStatsEquipo(): array
    {
        $reportes = $this->getHistorialEquipo();

        $total      = $reportes->count();
        $finalizados = $reportes->filter(fn ($r) =>
            in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Finalizado', 'Cerrado', 'Resuelto', 'Completado'])
        )->count();
        $pendientes = $reportes->filter(fn ($r) =>
            in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Pendiente', 'Sin atender', 'Nuevo'])
        )->count();

        $preventivos = $reportes->filter(fn ($r) =>
            str_contains(strtolower($r->tipoIntervencion?->nombre ?? ''), 'preventiv')
        )->count();
        $correctivos = $total - $preventivos;

        $ultimaFalla = $reportes->first()?->fecha;

        $diasPromedio = $reportes
            ->filter(fn ($r) =>
                in_array($r->ultimoEstado?->estadoReporte?->nombre, ['Finalizado', 'Cerrado', 'Resuelto', 'Completado'])
                && $r->ultimoEstado?->created_at
            )
            ->map(fn ($r) => Carbon::parse($r->fecha)->diffInDays($r->ultimoEstado->created_at))
            ->average();

        // Tendencia — comparar últimos 3 meses vs 3 meses anteriores
        $hace3meses = Carbon::now()->subMonths(3);
        $hace6meses = Carbon::now()->subMonths(6);

        $recientes = $reportes->filter(fn ($r) => Carbon::parse($r->fecha)->gte($hace3meses))->count();
        $anteriores = $reportes->filter(fn ($r) =>
            Carbon::parse($r->fecha)->between($hace6meses, $hace3meses)
        )->count();

        $tendencia = match (true) {
            $recientes > $anteriores => 'empeorando',
            $recientes < $anteriores => 'mejorando',
            default                  => 'estable',
        };

        return [
            'total'         => $total,
            'finalizados'   => $finalizados,
            'pendientes'    => $pendientes,
            'en_proceso'    => $total - $finalizados - $pendientes,
            'preventivos'   => $preventivos,
            'correctivos'   => $correctivos,
            'ultima_falla'  => $ultimaFalla,
            'dias_promedio' => $diasPromedio ? round($diasPromedio, 1) : null,
            'pct_resuelto'  => $total > 0 ? round(($finalizados / $total) * 100) : 0,
            'tendencia'     => $tendencia,
            'recientes'     => $recientes,
            'anteriores'    => $anteriores,
        ];
    }
}
