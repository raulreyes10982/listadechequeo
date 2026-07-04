<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Filament\Resources\AreaResource\RelationManagers\CargosRelationManager;
use App\Models\Area;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationIcon  = 'heroicon-o-view-columns';
    protected static ?string $navigationGroup = 'Organización';
    protected static ?string $navigationLabel = 'Áreas';
    protected static ?string $pluralLabel     = 'Áreas';
    protected static ?string $label           = 'Área';
    protected static ?int    $navigationSort  = 2;

    public static function getModalWidth(): MaxWidth
    {
        return MaxWidth::Large;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del área')
                    ->description('El nombre debe ser único dentro del mismo departamento.')
                    ->schema([
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->relationship('departamento', 'descripcion')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live() // ✅ revalida unique cuando cambia el departamento
                            ->columnSpanFull(),

                        TextInput::make('descripcion')
                            ->label('Nombre del área')
                            ->placeholder('Ej: Recursos Humanos, Almacén...')
                            ->required()
                            ->maxLength(250)
                            // ✅ Única por departamento, no global
                            ->unique(
                                table: 'areas',
                                column: 'descripcion',
                                ignoreRecord: true,
                                modifyRuleUsing: fn ($rule, Forms\Get $get) =>
                                    $rule->where('departamento_id', $get('departamento_id'))
                            )
                            ->validationMessages([
                                'unique' => 'Ya existe un área con este nombre en el departamento seleccionado.',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('departamento.descripcion')
                    ->label('Departamento')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Área')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                // ✅ Contador de cargos asociados
                Tables\Columns\TextColumn::make('cargos_count')
                    ->label('Cargos')
                    ->counts('cargos')
                    ->badge()
                    ->color('info')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Filtro por departamento
                Tables\Filters\SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'descripcion')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(static::getModalWidth()),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->disabled(fn (Area $record) => $record->cargos()->exists())
                    ->tooltip(fn (Area $record) =>
                        $record->cargos()->exists()
                            ? 'No se puede eliminar: tiene cargos asociados'
                            : null
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('descripcion', 'asc')
            // ✅ Agrupar visualmente por departamento
            ->groups([
                Tables\Grouping\Group::make('departamento.descripcion')
                    ->label('Departamento')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CargosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreas::route('/'),
            'edit'  => Pages\EditArea::route('/{record}/edit'),
        ];
    }
}
