<?php

// app/Filament/Resources/LocalResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\LocalResource\Pages;
use App\Models\Local;
use App\Models\Nomenclatura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class LocalResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static ?string $navigationLabel = 'Unidad comercial';
    protected static ?string $pluralLabel = 'Unidad comercial';   // Título en listado
    protected static ?string $label = 'Unidad comercial';         // Título en singular
    protected static ?string $navigationGroup = 'Localización';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre del Local')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(100),

                Select::make('nomenclatura_id')
                    ->label('Ubicación / Nomenclatura')
                    ->columnSpanFull()
                    ->relationship('nomenclatura', 'codigo')
                    ->options(\App\Models\Nomenclatura::pluck('codigo', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomenclatura.categoriaLocal.descripcion')
                    ->label('Unidad')
                    ->alignment('center')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nomenclatura.codigo')
                    ->label('N°')
                    ->alignment('center')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('NOMBRE')
                    ->alignment('center')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nomenclatura.piso')
                    ->label('PISO')
                    ->alignment('center')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nomenclatura.modulo')
                    ->label('BLOQUE')
                    ->alignment('center')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignment('center')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignment('center')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar')->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
            'index' => Pages\ListLocals::route('/'),
            //'create' => Pages\CreateLocal::route('/create'),
            //'edit' => Pages\EditLocal::route('/{record}/edit'),
        ];
    }
}
