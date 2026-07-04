<?php

namespace App\Filament\Widgets;

use App\Models\Reporte;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ReportesPendientesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    // ✅ Período activo y filtro de estado heredado de tarjetas
    public string  $periodo      = 'todos';   // ayer|semanal|mensual|trimestral|semestral|anual|todos|personalizado
    public ?string $fechaDesde   = null;
    public ?string $fechaHasta   = null;
    public ?string $estadoFiltro = null;       // recibido desde ReportesResumenWidget

    // ✅ Escucha el evento del widget de tarjetas
    #[On('filtroReporteSeleccionado')]
    public function actualizarFiltroEstado(?string $estado): void
    {
        $this->estadoFiltro = ($estado === 'all' || $estado === null) ? null : $estado;
    }

    public function setPeriodo(string $periodo): void
    {
        $this->periodo    = $periodo;
        $this->fechaDesde = null;
        $this->fechaHasta = null;
    }

    public function getHeading(): string
    {
        $label = match ($this->periodo) {
            'ayer'        => 'Ayer',
            'semanal'     => 'Esta semana',
            'mensual'     => 'Este mes',
            'trimestral'  => 'Último trimestre',
            'semestral'   => 'Último semestre',
            'anual'       => 'Este año',
            'personalizado' => 'Período personalizado',
            default       => 'Todos',
        };

        $estadoLabel = match ($this->estadoFiltro) {
            'critico'   => ' · Críticos',
            'pendiente' => ' · Pendientes',
            'en_proceso'=> ' · En proceso',
            'finalizado'=> ' · Finalizados',
            default     => '',
        };

        return "⚠️ Reportes — {$label}{$estadoLabel}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->buildQuery())
            ->headerActions([
                // ✅ Botones de período rápido
                Tables\Actions\Action::make('ayer')
                    ->label('Ayer')
                    ->size('sm')
                    ->color($this->periodo === 'ayer' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('ayer')),

                Tables\Actions\Action::make('semanal')
                    ->label('Semana')
                    ->size('sm')
                    ->color($this->periodo === 'semanal' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('semanal')),

                Tables\Actions\Action::make('mensual')
                    ->label('Mes')
                    ->size('sm')
                    ->color($this->periodo === 'mensual' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('mensual')),

                Tables\Actions\Action::make('trimestral')
                    ->label('Trimestre')
                    ->size('sm')
                    ->color($this->periodo === 'trimestral' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('trimestral')),

                Tables\Actions\Action::make('semestral')
                    ->label('Semestre')
                    ->size('sm')
                    ->color($this->periodo === 'semestral' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('semestral')),

                Tables\Actions\Action::make('anual')
                    ->label('Año')
                    ->size('sm')
                    ->color($this->periodo === 'anual' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('anual')),

                Tables\Actions\Action::make('todos')
                    ->label('Todos')
                    ->size('sm')
                    ->color($this->periodo === 'todos' ? 'primary' : 'gray')
                    ->action(fn () => $this->setPeriodo('todos')),

                // ✅ Filtro de rango de fechas personalizado
                Tables\Actions\Action::make('personalizado')
                    ->label('Rango de fechas')
                    ->icon('heroicon-m-calendar-days')
                    ->size('sm')
                    ->color($this->periodo === 'personalizado' ? 'primary' : 'gray')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->required(),
                        DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->periodo    = 'personalizado';
                        $this->fechaDesde = $data['desde'];
                        $this->fechaHasta = $data['hasta'];
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->badge()->color('gray')->alignment('center'),

                Tables\Columns\TextColumn::make('tipoReporte.descripcion')
                    ->label('Tipo de reporte')
                    ->searchable(),

                Tables\Columns\TextColumn::make('zona.descripcion')
                    ->label('Zona')
                    ->badge()->color('gray'),

                Tables\Columns\TextColumn::make('prioridad.descripcion')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'alta', 'urgente', 'crítica', 'critica' => 'danger',
                        'media'  => 'warning',
                        'baja'   => 'info',
                        default  => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'  => 'danger',
                        'En proceso' => 'warning',
                        'Finalizado', 'Cerrado', 'Resuelto' => 'success',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Registrado por')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('tiempo_abierto')
                    ->label('Tiempo abierto')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $horas = $record->created_at->diffInHours(now());
                        if ($horas < 24) return "{$horas}h";
                        $dias = intdiv($horas, 24);
                        $h    = $horas % 24;
                        return "{$dias}d {$h}h";
                    })
                    ->color(fn ($record) =>
                        $record->created_at->diffInHours(now()) >= 48 ? 'danger' : 'gray'
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Sin reportes para este período')
            ->emptyStateIcon('heroicon-o-check-badge');
    }

    protected function buildQuery(): Builder
    {
        $query = Reporte::query()->with(['tipoReporte', 'zona', 'prioridad', 'estado']);

        // ── Filtro por período ────────────────────────────────────────────
        match ($this->periodo) {
            'ayer'        => $query->whereDate('created_at', Carbon::yesterday()),
            'semanal'     => $query->whereBetween('created_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek(),
                            ]),
            'mensual'     => $query->whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year),
            'trimestral'  => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(3)->startOfDay(),
                                Carbon::now(),
                            ]),
            'semestral'   => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(6)->startOfDay(),
                                Carbon::now(),
                            ]),
            'anual'       => $query->whereYear('created_at', Carbon::now()->year),
            'personalizado' => $query->when($this->fechaDesde, fn ($q) =>
                                    $q->whereDate('created_at', '>=', $this->fechaDesde)
                                )->when($this->fechaHasta, fn ($q) =>
                                    $q->whereDate('created_at', '<=', $this->fechaHasta)
                                ),
            default       => null, // todos
        };

        // ── Filtro por estado (desde tarjetas) ────────────────────────────
        match ($this->estadoFiltro) {
            'critico'    => $query->whereHas('prioridad', fn ($q) =>
                                $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
                            )->whereHas('estado', fn ($q) =>
                                $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
                            ),
            'pendiente'  => $query->whereHas('estado', fn ($q) =>
                                $q->where('descripcion', 'Pendiente')),
            'en_proceso' => $query->whereHas('estado', fn ($q) =>
                                $q->where('descripcion', 'En proceso')),
            'finalizado' => $query->whereHas('estado', fn ($q) =>
                                $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])),
            default      => null,
        };

        return $query;
    }
}
