<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialEstadoReporteResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HistorialEstadoReporteResource\Pages;
use App\Models\HistorialEstadoReporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;




class HistorialEstadoReporteResource extends Resource
{
    protected static ?string $model = HistorialEstadoReporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Historia Reporte';
    protected static ?string $modelLabel = 'Historia Reporte';
    protected static ?string $pluralModelLabel = 'Historia Reporte';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Hidden::make('cambiado_por'),

            DateTimePicker::make('fecha_cambio')
                ->label('Fecha de cambio')
                ->required()
                ->default(now()),


            Select::make('reporte_tecnico_id')
            ->label('Reporte Técnico')
            ->relationship('reporteTecnico', 'id')
            ->searchable()
            ->required(),

            Select::make('estado_reporte_id')
                ->label('Estado')
                ->relationship('estado', 'nombre')
                ->required(),

            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reporteTecnico.id')
                ->label('Reporte Técnico'),

            TextColumn::make('estado.nombre')
                ->label('Estado')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'Pendiente' => 'warning',
                    'En proceso' => 'info',
                    'Finalizado' => 'success',
                    'Cancelado' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('cambiado_por')
                ->label('Cambiado por'),

            Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),
                    //->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Puedes agregar filtros aquí si deseas
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
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
            // Aquí puedes registrar RelationManagers si deseas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistorialEstadoReportes::route('/'),
            'create' => Pages\CreateHistorialEstadoReporte::route('/create'),
            'edit' => Pages\EditHistorialEstadoReporte::route('/{record}/edit'),
        ];
    }
}