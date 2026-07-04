<?php

namespace App\Filament\Resources\DepartamentoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AreasRelationManager extends RelationManager
{
    protected static string $relationship = 'areas';

    protected static ?string $title = 'Áreas';
    protected static ?string $icon  = 'heroicon-o-view-columns';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('descripcion')
                    ->label('Nombre del área')
                    ->placeholder('Ej: Recursos Humanos, Almacén...')
                    ->required()
                    ->maxLength(250)
                    // ✅ Única dentro del mismo departamento, no globalmente
                    ->unique(
                        table: 'areas',
                        column: 'descripcion',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) =>
                            $rule->where('departamento_id', $this->getOwnerRecord()->id)
                    )
                    ->validationMessages([
                        'unique' => 'Ya existe un área con este nombre en este departamento.',
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
                    ->label('Área')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cargos_count')
                    ->label('Cargos')
                    ->counts('cargos')
                    ->badge()
                    ->color('info')
                    ->alignment('center'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar área')
                    ->modalHeading('Nueva área'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->disabled(fn ($record) => $record->cargos()->exists())
                    ->tooltip(fn ($record) =>
                        $record->cargos()->exists()
                            ? 'No se puede eliminar: tiene cargos asociados'
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
