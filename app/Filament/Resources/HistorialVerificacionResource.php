<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialVerificacionResource\Pages;
use App\Models\HistorialVerificacion;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class HistorialVerificacionResource extends Resource
{
    protected static ?string $model = HistorialVerificacion::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Permisos';
    protected static ?string $modelLabel = 'Personal verificado'; // singular
    protected static ?string $pluralModelLabel = 'Personal verificado'; // plural
    protected static ?string $navigationLabel = 'Personal verificado';
    protected static ?int $navigationSort = 4;

    

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('fecha')
                ->alignment('center')
                ->date('d/M/Y')
                ->label('Fecha'),
            TextColumn::make('hora')
                ->alignment('center')
                ->label('Hora'),
            TextColumn::make('permiso.tipoPermiso.descripcion')
                ->alignment('center')
                ->label('Tipo permiso')
                ->toggleable(),
            TextColumn::make('nombre')
                ->alignment('center')
                ->label('Nombre')
                ->searchable(),
            TextColumn::make('documento')
                ->label('Documento')
                ->alignment('center')
                ->searchable(), 
            TextColumn::make('verificadopor')
                ->label('Verificado por')
                ->searchable(),
            TextColumn::make('estado')
                ->label('Estado')
                ->alignment('center')
                ->badge()
                ->colors([
                    'success' => 'Autorizado', // verde
                    //'danger' => 'Inactivo', // rojo (opcional)
                    //'warning' => 'Pendiente', // amarillo (opcional)
                ]),
        ])
        ->filters([])
        ->actions([]) // sin acciones de edición
        ->bulkActions([]); // sin bulk actions
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialVerificaciones::route('/'),
        ];
    }
}
