<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoPermisoResource\Pages;
use App\Filament\Resources\TipoPermisoResource\RelationManagers;
use App\Models\TipoPermiso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoPermisoResource extends Resource
{
    protected static ?string $model = TipoPermiso::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Permisos';
    protected static ?string $navigationLabel = 'Tipo Permiso';
    protected static ?string $pluralLabel = 'Tipo Permiso';   // Título en listado
    protected static ?string $label = 'Tipo Permiso';         // Título en singular
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->rule('unique:tipo_permisos,descripcion')
                    ->columnSpanFull()
                    ->required(),
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
            'index' => Pages\ListTipoPermisos::route('/'),
            //'create' => Pages\CreateTipoPermiso::route('/create'),
            //'edit' => Pages\EditTipoPermiso::route('/{record}/edit'),
        ];
    }
}
