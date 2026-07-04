<?php

namespace App\Filament\Resources\AreaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CargosRelationManager extends RelationManager
{
    protected static string $relationship = 'cargos';

    protected static ?string $title = 'Cargos';
    protected static ?string $icon  = 'heroicon-o-briefcase';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->label('Nombre del cargo')
                    ->placeholder('Ej: Supervisor, Auxiliar, Coordinador...')
                    ->required()
                    ->maxLength(250)
                    // ✅ Único dentro de la misma área, no globalmente
                    ->unique(
                        table: 'cargos',
                        column: 'descripcion',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) =>
                            $rule->where('area_id', $this->getOwnerRecord()->id)
                    )
                    ->validationMessages([
                        'unique' => 'Ya existe un cargo con este nombre en esta área.',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Cargo')
                    ->searchable()
                    ->weight('bold'),

                // ✅ Cuántos colaboradores tienen este cargo
                Tables\Columns\TextColumn::make('colaboradores_count')
                    ->label('Colaboradores')
                    ->counts('colaboradores')
                    ->badge()
                    ->color('success')
                    ->alignment('center'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar cargo')
                    ->modalHeading('Nuevo cargo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->disabled(fn ($record) => $record->colaboradores()->exists())
                    ->tooltip(fn ($record) =>
                        $record->colaboradores()->exists()
                            ? 'No se puede eliminar: hay colaboradores con este cargo'
                            : null
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
