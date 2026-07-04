<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MinutaResource\Pages;
use App\Filament\Resources\MinutaResource\RelationManagers;
use App\Models\Minuta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MinutaResource extends Resource
{
    protected static ?string $model = Minuta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Minuta';
    protected static ?string $modelLabel       = 'Minuta';
    protected static ?string $pluralModelLabel = 'Minuta';
    protected static ?int    $navigationSort   = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMinutas::route('/'),
            'create' => Pages\CreateMinuta::route('/create'),
            'edit' => Pages\EditMinuta::route('/{record}/edit'),
        ];
    }
}
