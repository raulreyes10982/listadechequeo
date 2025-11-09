<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoReporteResource\Pages;
use App\Models\TipoReporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TipoReporteResource extends Resource
{
    protected static ?string $model = TipoReporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Tipos de Reportes';
    protected static ?string $modelLabel = 'Tipo de Reporte';
    protected static ?string $pluralModelLabel = 'Tipos de Reporte';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('categoria_reporte_id')
                    ->label('Categoría de Reporte')
                    ->relationship('categoria', 'descripcion')
                    ->required()
                    ->columnSpanFull()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250),

                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categoria.descripcion')
                    ->label('Categoría')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
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
            ])->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()->label('eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoReportes::route('/'),
            //'create' => Pages\CreateTipoReporte::route('/create'),
            //'edit' => Pages\EditTipoReporte::route('/{record}/edit'),
        ];
    }
}
