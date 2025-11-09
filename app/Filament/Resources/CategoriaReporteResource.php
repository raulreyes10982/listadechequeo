<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaReporteResource\Pages;
use App\Filament\Resources\CategoriaReporteResource\RelationManagers;
use App\Models\CategoriaReporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriaReporteResource extends Resource
{
    protected static ?string $model = CategoriaReporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Categorías de Reporte';
    protected static ?string $pluralModelLabel = 'Categorías de Reporte';
    protected static ?string $modelLabel = 'Categoría de Reporte';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    ->unique(ignoreRecord: true)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()->label('eliminar'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoriaReportes::route('/'),
            //'create' => Pages\CreateCategoriaReporte::route('/create'),
            //'edit' => Pages\EditCategoriaReporte::route('/{record}/edit'),
        ];
    }
}
