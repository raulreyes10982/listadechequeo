<?php

namespace App\Filament\Resources\PermisoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrabajadoresRelationManager extends RelationManager
{
    protected static string $relationship = 'trabajadores';
    protected static ?string $recordTitleAttribute = 'nombre';
    protected static ?string $title = 'Trabajadores';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nombre')
                ->label('Nombre completo')
                ->required()
                ->maxLength(150),

            TextInput::make('documento')
                ->label('Documento')
                ->required()
                ->maxLength(50),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('documento')->label('Documento')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Creado'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Agregar trabajador'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
            ]);
    }
}
