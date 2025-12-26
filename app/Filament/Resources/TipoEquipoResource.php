<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoEquipoResource\Pages;
use App\Filament\Resources\TipoEquipoResource\RelationManagers;
use App\Models\TipoEquipo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;


class TipoEquipoResource extends Resource
{
    protected static ?string $model = TipoEquipo::class;

    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Tipo de Equipos';
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 1;

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    ->rule('unique:tipo_equipos,descripcion')
                    ->default(null),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListTipoEquipos::route('/'),
            //'create' => Pages\CreateTipoEquipo::route('/create'),
            //'edit' => Pages\EditTipoEquipo::route('/{record}/edit'),
        ];
    }
}
