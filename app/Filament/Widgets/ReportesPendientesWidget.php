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
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public string  $periodo      = 'todos';
    public ?string $fechaDesde   = null;
    public ?string $fechaHasta   = null;
    public ?string $estadoFiltro = null;

    // ✅ Escucha filtro de estado desde las tarjetas
    #[On('filtroReporteSeleccionado')]
    public function actualizarFiltroEstado(mixed $estado = null): void
    {
        $this->estadoFiltro = (is_null($estado) || $estado === 'all') ? null : (string) $estado;
    }

    public function setPeriodo(string $periodo): void
    {
        $this->periodo    = $periodo;
        $this->fechaDesde = null;
        $this->fechaHasta = null;

        // ✅ Notificar a gráfica y donas
        $this->dispatch('periodoCambiado',
            periodo: $periodo,
            desde: null,
            hasta: null
        );
    }

    public function getHeading(): string
    {
        $pLabel = match ($this->periodo) {
            'ayer'          => 'Ayer',
            'semanal'       => 'Esta semana',
            'mensual'       => 'Este mes',
            'trimestral'    => 'Último trimestre',
            'semestral'     => 'Último semestre',
            'anual'         => 'Este año',
            'personalizado' => ($this->fechaDesde && $this->fechaHasta)
                ? Carbon::parse($this->fechaDesde)->format('d/m/Y') . ' → ' . Carbon::parse($this->fechaHasta)->format('d/m/Y')
                : 'Período personalizado',
            default         => 'Todos los reportes',
        };

        $eLabel = match ($this->estadoFiltro) {
            'critico'    => ' · 🔴 Críticos',
            'pendiente'  => ' · 🟡 Pendientes',
            'en_proceso' => ' · 🔵 En proceso',
            'finalizado' => ' · 🟢 Finalizados',
            default      => '',
        };

        return "📋 {$pLabel}{$eLabel}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->buildQuery())
            ->headerActions([
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

                // ✅ Rango personalizado — también despacha periodoCambiado
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
                            ->required()
                            ->afterOrEqual('desde'),
                    ])
                    ->action(function (array $data) {
                        $this->periodo    = 'personalizado';
                        $this->fechaDesde = $data['desde'];
                        $this->fechaHasta = $data['hasta'];

                        // ✅ Notificar a gráfica y donas con el rango exacto
                        $this->dispatch('periodoCambiado',
                            periodo: 'personalizado',
                            desde: $data['desde'],
                            hasta: $data['hasta']
                        );
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
                        'media'   => 'warning',
                        'baja'    => 'info',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'                          => 'warning',
                        'En proceso', 'Asignado'            => 'info',
                        'Finalizado', 'Cerrado', 'Resuelto' => 'success',
                        default                             => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Registrado por')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('tiempo_abierto')
                    ->label('Tiempo abierto')
                    ->badge()
                    ->alignment('center')
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
            ->emptyStateDescription('Cambia el filtro o el período para ver más resultados.')
            ->emptyStateIcon('heroicon-o-document-magnifying-glass');
    }

    protected function buildQuery(): Builder
    {
        $query = Reporte::query()->with(['tipoReporte', 'zona', 'prioridad', 'estado']);

        // ── Filtro de período ─────────────────────────────────────────────
        match ($this->periodo) {
            'ayer'       => $query->whereDate('created_at', Carbon::yesterday()),
            'semanal'    => $query->whereBetween('created_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek()]),
            'mensual'    => $query->whereMonth('created_at', Carbon::now()->month)
                                  ->whereYear('created_at', Carbon::now()->year),
            'trimestral' => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(3)->startOfDay(), Carbon::now()]),
            'semestral'  => $query->whereBetween('created_at', [
                                Carbon::now()->subMonths(6)->startOfDay(), Carbon::now()]),
            'anual'      => $query->whereYear('created_at', Carbon::now()->year),
            'personalizado' => $query
                ->when($this->fechaDesde, fn ($q) => $q->whereDate('created_at', '>=', $this->fechaDesde))
                ->when($this->fechaHasta, fn ($q) => $q->whereDate('created_at', '<=', $this->fechaHasta)),
            default      => null,
        };

        // ── Filtro de estado (desde las tarjetas) ─────────────────────────
        match ($this->estadoFiltro) {
            'critico'    => $query
                ->whereHas('prioridad', fn ($q) =>
                    $q->whereIn('descripcion', ['Alta', 'Urgente', 'Crítica', 'Critica'])
                )
                ->whereHas('estado', fn ($q) =>
                    $q->whereNotIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])
                ),
            'pendiente'  => $query->whereHas('estado', fn ($q) =>
                                $q->where('descripcion', 'Pendiente')),
            'en_proceso' => $query->whereHas('estado', fn ($q) =>
                                $q->whereIn('descripcion', ['En proceso', 'Asignado'])),
            'finalizado' => $query->whereHas('estado', fn ($q) =>
                                $q->whereIn('descripcion', ['Finalizado', 'Cerrado', 'Resuelto'])),
            default      => null,
        };

        return $query;
    }
}
