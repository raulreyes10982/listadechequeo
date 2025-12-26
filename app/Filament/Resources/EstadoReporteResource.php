<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoReporteResource\Pages;
use App\Filament\Resources\EstadoReporteResource\RelationManagers;
use App\Models\EstadoReporte;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Label;

class EstadoReporteResource extends Resource
{
    protected static ?string $model = EstadoReporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Estado Reporte';
    protected static ?string $modelLabel = 'Estado Reporte';
    protected static ?string $pluralModelLabel = 'Estado Reporte';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    ->default(null)
                    ->rules([ Rule::unique('estado_reportes', 'nombre'), ])
                ]);
    } 

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
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
                Tables\Actions\EditAction::make()->label('editar')->modalWidth(('lg')),
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
            'index' => Pages\ListEstadoReportes::route('/'),
            //'create' => Pages\CreateEstadoReporte::route('/create'),
            //'edit' => Pages\EditEstadoReporte::route('/{record}/edit'),
        ];
    }
}
