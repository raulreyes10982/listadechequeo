<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistorialVerificacionResource\Pages;
use App\Filament\Resources\HistorialVerificacionResource\RelationManagers;
use App\Models\HistorialVerificacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistorialVerificacionResource extends Resource
{
    //protected static ?string $model = HistorialVerificacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
        ->modifyQueryUsing(fn (Builder $query) =>
            $query->with(['permiso', 'trabajador'])->verificados()
        )
        ->columns([
            Tables\Columns\TextColumn::make('fecha')->label('Fecha')->date()->sortable(),
            Tables\Columns\TextColumn::make('hora')->label('Hora')->sortable(),
            Tables\Columns\TextColumn::make('verificadopor')->label('Verificado por'),
            Tables\Columns\TextColumn::make('trabajador.nombre')->label('Nombre'),
            Tables\Columns\TextColumn::make('trabajador.documento')->label('Documento'),
            Tables\Columns\TextColumn::make('permiso.tipoPermiso.descripcion')->label('Tipo Permiso'),
            Tables\Columns\TextColumn::make('contratista_o_unidad')->label('Contratista / Unidad'),
            Tables\Columns\TextColumn::make('dias_autorizados')->label('Días Autorizados'),
        ])
        ->filters([])
        ->actions([])
        ->bulkActions([]);
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
            'index' => Pages\ListHistorialVerificacions::route('/'),
            'create' => Pages\CreateHistorialVerificacion::route('/create'),
            'edit' => Pages\EditHistorialVerificacion::route('/{record}/edit'),
        ];
    }
}
