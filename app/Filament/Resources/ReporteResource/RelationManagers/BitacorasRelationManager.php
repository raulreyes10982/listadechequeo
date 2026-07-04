<?php

namespace App\Filament\Resources\ReporteResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class BitacorasRelationManager extends RelationManager
{
    protected static string $relationship = 'bitacoras';
    protected static ?string $title = 'Bitácora de Estados';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ CORRECCIÓN: el modelo Estado tiene campo "descripcion", no "nombre"
                TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'  => 'danger',
                        'En proceso' => 'warning',
                        'Verificado' => 'info',
                        'Finalizado' => 'success',
                        default      => 'gray',
                    }),

                TextColumn::make('registrado_por')
                    ->label('Registrado por'),

                TextColumn::make('fecha')
                    ->date('d/M/Y')
                    ->label('Fecha'),

                TextColumn::make('hora')
                    ->time('H:i')
                    ->label('Hora'),

                TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'desc');
    }
}
