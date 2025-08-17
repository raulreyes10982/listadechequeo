<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Filament\Resources\AreaResource\RelationManagers;
use App\Models\Area;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section; 


class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'Organización';
    protected static ?string $navigationLabel = 'Áreas';
    protected static ?string $pluralLabel = 'Áreas';   // Título en listado
    protected static ?string $label = 'Áreas';         // Título en singular
    protected static ?int $navigationSort = 2;




    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('departamento_id')
                    ->required()
                    ->columnSpan(1)
                    ->relationship('departamento', 'descripcion')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(250)
                    ->columnSpan(1)
                    ->default(null),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departamento.descripcion')
                    ->label('Departamento')
                    //->alignment('center')
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Area')
                    //->alignment('center')
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
            'index' => Pages\ListAreas::route('/'),
            //'create' => Pages\CreateArea::route('/create'),
            //'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
