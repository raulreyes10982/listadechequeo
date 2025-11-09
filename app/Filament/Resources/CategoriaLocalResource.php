<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriaLocalResource\Pages;
use App\Models\CategoriaLocal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoriaLocalResource extends Resource
{
    protected static ?string $model = CategoriaLocal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Localización';
    protected static ?string $navigationLabel = 'Categoria';
    protected static ?string $pluralLabel = 'Categoria';   // Título en listado
    protected static ?string $label = 'Categoria';         // Título en singular
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->label('Nombre de la Categoría')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(null)
                    ->columnSpanFull()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->label('ID'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable(),

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
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()->label('eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategoriaLocals::route('/'),
            //'create' => Pages\CreateCategoriaLocal::route('/create'),
            //'edit' => Pages\EditCategoriaLocal::route('/{record}/edit'),
        ];
    }
}