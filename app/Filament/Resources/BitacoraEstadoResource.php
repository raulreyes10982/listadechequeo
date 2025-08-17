<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraEstadoResource\Pages;
use App\Models\BitacoraEstado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;


class BitacoraEstadoResource extends Resource
{
    protected static ?string $model = BitacoraEstado::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Bitácora del reporte';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Forms\Components\Hidden::make('registrado_por'),

                Forms\Components\TimePicker::make('hora')
                    ->label('Hora')
                    ->format('H:i')
                    ->default(Carbon::now()->format('H:i'))
                    ->hidden(),

                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->format('Y-m-d')
                    ->default(Carbon::now()->format('Y-m-d'))
                    ->hidden(),

                Forms\Components\Select::make('reporte_id')
                    ->label('Reporte')
                    ->relationship('reporte', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $partes = [];

                        if (!empty($record->tipoReporte?->descripcion)) {
                            $partes[] = $record->tipoReporte->descripcion;
                        }

                        if (!empty($record->ubicacion?->descripcion)) {
                            $partes[] = $record->ubicacion->descripcion;
                        }

                        if (!empty($record->local?->option_label)) {
                            $partes[] = 'Local ' . $record->local->option_label;
                        }

                        return implode(' -> ', $partes);
                    })
                    ->searchable(['id', 'tipoReporte.descripcion', 'ubicacion.descripcion', 'local.option_label'])
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(3),

                Forms\Components\Select::make('estado_id')
                    ->relationship('estado', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(3),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Observaciones')
                    ->maxLength(500)
                    ->rows(5)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }
 
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registrado_por')
                    ->label('Subido por')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('reporte_info')
                    ->label('Reporte')
                    ->getStateUsing(function ($record) {
                        $partes = [];

                        if (!empty($record->reporte?->tipoReporte?->descripcion)) {
                            $partes[] = $record->reporte->tipoReporte->descripcion;
                        }
                        if (!empty($record->reporte?->ubicacion?->descripcion)) {
                            $partes[] = $record->reporte->ubicacion->descripcion;
                        }
                        if (!empty($record->reporte?->local?->option_label)) {
                            $partes[] = 'Local ' . $record->reporte->local->option_label;
                        }

                        return implode(' -> ', $partes);
                    })
                    ->alignment('center')
                    ->sortable()
                    ->searchable(),


                Tables\Columns\TextColumn::make('estado.descripcion')
                        ->label('Estado')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'Pendiente'   => 'danger',    // rojo
                            'En proceso'  => 'warning',   // amarillo
                            'Verificado'  => 'info',      // azul claro
                            'Finalizado'  => 'success',   // verde
                            default       => 'gray',      // gris por defecto
                        })
                        ->sortable()
                        ->searchable()
                        ->alignment('center'),
                
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap()
                    ->alignment('center')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                
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
