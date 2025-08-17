<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialEstadoReporteResource\Pages;
use App\Models\HistorialEstadoReporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use Filament\Forms\Components\{Hidden, TimePicker, DatePicker, Select, Textarea};



class HistorialEstadoReporteResource extends Resource
{
    public static function canCreate(): bool
    {
        return false;
    }
    protected static ?string $model = HistorialEstadoReporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Historia Reporte';
    protected static ?string $modelLabel = 'Historia Reporte';
    protected static ?string $pluralModelLabel = 'Bitacora reporte de equipos';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('cambiado_por'),

            DateTimePicker::make('fecha')
                ->label('Fecha')
                ->required()
                ->default(now()),

            DateTimePicker::make('hora')
                ->label('Fecha')
                ->format('Y-m-d')
                ->default(Carbon::now()->format('Y-m-d'))
                ->hidden(),

            Select::make('reporte_tecnico_id')
                ->label('Reporte Técnico')
                ->relationship('reporte_tecnicos', 'id')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('estado_reporte_id')
                ->label('Estado')
                ->relationship('estadoReporte', 'nombre') // <- relación corregida
                ->searchable()
                ->preload()
                ->required(),
            Textarea::make('descripcion')
                ->label('Observaciones')
                ->rows(4)
                ->nullable(),
            
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            TextColumn::make('reporteTecnico.id')
                ->label('N° Reporte')
                ->sortable()
                ->searchable()
                ->alignment('center'),

            TextColumn::make('fecha')
                ->label('Fecha')
                ->alignment('center')
                ->date('d/m/Y'),

            TextColumn::make('hora')
                ->label('Hora')
                ->alignment('center')
                ->time('H:i'),
            
            TextColumn::make('cambiado_por')
                ->label('Responsable')
                ->sortable()
                ->searchable()
                ->alignment('center'),
            
            TextColumn::make('reporteTecnico.tipoIntervencion.nombre')
                ->label('Tipo Intervenció')
                ->sortable()
                ->searchable()
                ->alignment('center'),

            TextColumn::make('reporteTecnico.equipo.tipoEquipo.descripcion')
                ->label('Equipo')
                ->sortable()
                ->searchable()
                ->alignment('center'),

            TextColumn::make('estadoReporte.nombre') // <- relación corregida
                ->label('Estado')
                ->badge()
                ->sortable()
                ->searchable()
                ->alignment('center')
                ->color(fn ($state) => match ($state) {
                    'Pendiente' => 'warning',
                    'En proceso' => 'info',
                    'Finalizado' => 'success',
                    'Cancelado' => 'danger',
                    default => 'gray',
                }), 
            TextColumn::make('descripcion')
                ->label('Observaciones')
                ->wrap()
                ->alignment('center')
                ->sortable()
                ->searchable(),
                
            Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([])
        ->actions([
            //Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
            Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // Puedes registrar RelationManagers aquí si deseas mostrar el historial desde ReporteTecnicoResource
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialEstadoReportes::route('/'),
            //'create' => Pages\CreateHistorialEstadoReporte::route('/create'),
            //'edit' => Pages\EditHistorialEstadoReporte::route('/{record}/edit'),
        ];
    }
}
