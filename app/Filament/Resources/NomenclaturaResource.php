<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NomenclaturaResource\Pages;
use App\Filament\Resources\NomenclaturaResource\RelationManagers;
use App\Models\Nomenclatura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NomenclaturaResource extends Resource
{
    protected static ?string $model = Nomenclatura::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Localización';
    protected static ?string $navigationLabel = 'Nomenclaturas';
    protected static ?string $pluralLabel = 'Nomenclaturas';   // Título en listado
    protected static ?string $label = 'Nomenclaturas';         // Título en singular
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('categoria_local_id')
                    ->label('Unidad Comercial')
                    ->relationship('categoriaLocal', 'descripcion')
                    ->searchable()
                    ->columnSpanFull()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('piso')
                    ->label('Piso')
                    ->columnSpanFull()
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('modulo')
                    ->label('Módulo')
                    ->columnSpanFull()
                    ->maxLength(50)
                    ->nullable(),
                
                Forms\Components\TextInput::make('codigo')
                    ->label('Código de Nomenclatura')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            
            ->columns([
                Tables\Columns\TextColumn::make('categoriaLocal.descripcion')
                    ->label('Unidad')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('codigo')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('piso')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('modulo')->label('Módulo')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                Tables\Columns\TextColumn::make('created_at')
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
            'index' => Pages\ListNomenclaturas::route('/'),
            //'create' => Pages\CreateNomenclatura::route('/create'),
            //'edit' => Pages\EditNomenclatura::route('/{record}/edit'),
        ];
    }
}
