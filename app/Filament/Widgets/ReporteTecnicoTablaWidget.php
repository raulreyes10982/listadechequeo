<?php

namespace App\Filament\Widgets;

use App\Models\ReporteTecnico;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ReporteTecnicoTablaWidget extends BaseWidget
{
       protected static ?int $sort = 7;

    protected static ?string $heading = 'Reportes Técnicos de Equipos';

    protected ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ReporteTecnico::query()
                    ->with([
                        'equipo.tipoEquipo',
                        'tipoIntervencion',
                        'ultimoEstado.estadoReporte',
                    ])
                    ->whereHas(
                        'ultimoEstado.estadoReporte',
                        fn ($q) => $q->whereNotIn('nombre', [
                            'Finalizado',
                            'Cerrado',
                            'Resuelto',
                            'Completado',
                        ])
                    )
                    ->orderBy('fecha')
                    ->orderBy('hora')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->alignment('center')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->alignment('center')
                    ->sortable(),

                Tables\Columns\TextColumn::make('equipo.tipoEquipo.descripcion')
                    ->label('Equipo')
                    ->formatStateUsing(
                        fn ($state, $record) =>
                            ($record->equipo?->tipoEquipo?->descripcion ?? '—')
                            . ' — ' .
                            ($record->equipo?->descripcion ?? '')
                    )
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipoIntervencion.nombre')
                    ->label('Tipo intervención')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Registrado por')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('ultimoEstado.estadoReporte.nombre')
                    ->label('Estado')
                    ->badge()
                    ->color(
                        fn ($state) => match ($state) {
                            'Pendiente' => 'danger',
                            'En proceso' => 'warning',
                            'Cancelado' => 'gray',
                            default => 'info',
                        }
                    ),

                Tables\Columns\TextColumn::make('dias_abierto')
                    ->label('Días abierto')
                    ->alignment('center')
                    ->badge()
                    ->getStateUsing(
                        fn ($record) =>
                            Carbon::parse($record->fecha)
                                ->diffInDays(Carbon::today())
                    )
                    ->color(
                        fn ($state) => match (true) {
                            $state >= 5 => 'danger',
                            $state >= 2 => 'warning',
                            default => 'success',
                        }
                    )
                    ->formatStateUsing(
                        fn ($state) =>
                            $state === 0
                                ? 'Hoy'
                                : "{$state} día(s)"
                    ),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->descripcion)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'asc')
            ->emptyStateHeading('✅ Sin reportes pendientes')
            ->emptyStateDescription('Todos los reportes técnicos han sido atendidos.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver')
            ->paginated([5, 10, 25]);
    }
}