<?php

namespace App\Filament\Widgets;

use App\Models\Permiso;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PermisosVencenWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected static ?string $pollingInterval = '300s';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '📋 Permisos de trabajo que vencen pronto';

    public function table(Table $table): Table
    {
        $hoy     = Carbon::today();
        $en7dias = Carbon::today()->addDays(7);

        return $table
            ->query(
                Permiso::query()
                    ->with(['colaborador', 'contratistas', 'local', 'tipoPermiso', 'trabajadores'])
                    ->whereBetween('fecha_fin_trabajo', [$hoy, $en7dias])
                    ->orderBy('fecha_fin_trabajo')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tipoPermiso.descripcion')
                    ->label('Tipo permiso')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tercero')
                    ->label('Contratista / Unidad')
                    ->getStateUsing(fn ($record) =>
                        $record->contratistas?->descripcion
                            ?? $record->local?->nombre
                            ?? '—'
                    ),

                Tables\Columns\TextColumn::make('trabajadores_count')
                    ->label('Trabajadores')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) => $record->trabajadores->count()),

                Tables\Columns\TextColumn::make('fecha_inicio_trabajo')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fecha_fin_trabajo')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('dias_restantes')
                    ->label('Días restantes')
                    ->alignment('center')
                    ->badge()
                    ->getStateUsing(fn ($record) =>
                        Carbon::today()->diffInDays($record->fecha_fin_trabajo, false)
                    )
                    ->color(fn ($state) => match (true) {
                        $state === 0  => 'danger',
                        $state <= 2   => 'danger',
                        $state <= 5   => 'warning',
                        default       => 'success',
                    })
                    ->formatStateUsing(fn ($state) =>
                        $state === 0 ? 'Vence hoy' : "{$state} días"
                    ),

                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Autorizado por')
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('✅ Sin permisos próximos a vencer')
            ->emptyStateDescription('No hay permisos de trabajo que venzan en los próximos 7 días.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->paginated(false);
    }
}
