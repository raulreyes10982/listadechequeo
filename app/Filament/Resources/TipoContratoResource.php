<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoContratoResource\Pages;
use App\Filament\Resources\TipoContratoResource\RelationManagers;
use App\Models\TipoContrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoContratoResource extends Resource
{
    protected static ?string $model = TipoContrato::class;

    protected static ?string $navigationGroup = 'Datos Personales';
    protected static ?string $navigationLabel = 'Tipo Contrato';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(250)
                    ->columnSpanFull()
                    ->required()
                    ->rule('unique:tipo_contratos,descripcion')
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
            'index' => Pages\ListTipoContratos::route('/'),
            //'create' => Pages\CreateTipoContrato::route('/create'),
            //'edit' => Pages\EditTipoContrato::route('/{record}/edit'),
        ];
    }
}
