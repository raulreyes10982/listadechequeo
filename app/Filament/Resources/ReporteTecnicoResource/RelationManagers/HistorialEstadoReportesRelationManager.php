<?php

namespace App\Filament\Resources\ReporteTecnicoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class HistorialEstadoReportesRelationManager extends RelationManager
{
    protected static string $relationship = 'historialEstadoReportes';
    protected static ?string $title = 'Historial de Estados';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('estadoReporte.nombre')->label('Estado')->badge(),
            TextColumn::make('cambiado_por')->label('Cambiado por'),
            TextColumn::make('fecha')->date('d/M/Y'),
            TextColumn::make('hora')->time('H:i'),
        ])->defaultSort('fecha', 'desc');
    }
}
