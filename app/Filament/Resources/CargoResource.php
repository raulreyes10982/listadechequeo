<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoResource\Pages;
use App\Filament\Resources\CargoResource\RelationManagers;
use App\Models\Cargo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;


class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;
    
    protected static ?string $navigationGroup = 'Organización';
    protected static ?string $navigationLabel = 'Cargos';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('area_id')
                    ->required()
                    ->columnSpan(1)
                    ->relationship('area', 'descripcion')
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
                Tables\Columns\TextColumn::make('descripcion')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('area.descripcion')
                    ->label('Area')
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
            'index' => Pages\ListCargos::route('/'),
           //'create' => Pages\CreateCargo::route('/create'),
            //'edit' => Pages\EditCargo::route('/{record}/edit'),
        ];
    }
}
