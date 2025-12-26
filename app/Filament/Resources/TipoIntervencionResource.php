<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoIntervencionResource\Pages;
use App\Filament\Resources\TipoIntervencionResource\RelationManagers;
use App\Models\TipoIntervencion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Label;

class TipoIntervencionResource extends Resource
{
    protected static ?string $model = TipoIntervencion::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Tipo Intervencion';
    protected static ?string $modelLabel = 'Tipo Intervencion';
    protected static ?string $pluralModelLabel = 'Tipo Intervencion';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    ->default(null)
                    ->rules([ Rule::unique('tipo_intervencions', 'nombre'), ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Descripción')
                    ->sortable()
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
            'index' => Pages\ListTipoIntervencions::route('/'),
            //'create' => Pages\CreateTipoIntervencion::route('/create'),
            //'edit' => Pages\EditTipoIntervencion::route('/{record}/edit'),
        ];
    }
}
