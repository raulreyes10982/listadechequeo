<?php

namespace App\Filament\Resources\ReporteResource\RelationManagers;

use Filament\Forms;
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
                TextColumn::make('estado.nombre')->label('Estado'),
                TextColumn::make('registrado_por')->label('registrado por'),
                TextColumn::make('fecha')->date('d/M/Y'),
                TextColumn::make('hora')->time('H:i'),
            ])->defaultSort('fecha', 'desc');
            
    }
}
