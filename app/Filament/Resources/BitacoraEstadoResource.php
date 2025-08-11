<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraEstadoResource\Pages;
use App\Models\BitacoraEstado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BitacoraEstadoResource extends Resource
{
    protected static ?string $model = BitacoraEstado::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reporte_id')
                    ->label('Reporte')
                    ->relationship('reporte', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $tipo = $record->tipoReporte?->descripcion ?? 'Sin tipo';
                        $ubicacion = $record->ubicacion?->descripcion ?? '';
                        $unidadPrivada = $record->local?->option_label;
                        $ubicacionCompleta = $unidadPrivada
                            ? "{$ubicacion} Local {$unidadPrivada}"
                            : $ubicacion;
                        return "{$tipo} - {$ubicacionCompleta}";
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->searchable()
                    ->required(),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reporte.tipoReporte.descripcion')
                    ->label('Tipo de Reporte')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('reporte.ubicacion_y_unidad')
                    ->label('Ubicación')
                    ->getStateUsing(function ($record) {
                        $ubicacion = $record->reporte?->ubicacion?->descripcion ?? '';
                        $unidadPrivada = $record->reporte?->local?->option_label;
                        return $unidadPrivada
                            ? "{$ubicacion} Local {$unidadPrivada}"
                            : $ubicacion;
                    })
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->alignment('center'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBitacoraEstados::route('/'),
            'create' => Pages\CreateBitacoraEstado::route('/create'),
            'edit' => Pages\EditBitacoraEstado::route('/{record}/edit'),
        ];
    }
}
