<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoResource\Pages;
use App\Models\Cargo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;

    protected static ?string $navigationGroup = 'Organización';
    protected static ?string $navigationLabel = 'Cargos';
    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    protected static ?int    $navigationSort  = 3;

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
    | FORMULARIO — selects en cascada Departamento → Área
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ubicación organizacional')
                    ->description('Selecciona primero el departamento para filtrar las áreas disponibles.')
                    ->schema([
                        // ✅ Select intermedio — no se guarda, solo filtra el de área
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->options(\App\Models\Departamento::pluck('descripcion', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->dehydrated(false)
                            // Precargar al editar, desde la relación área → departamento
                            ->afterStateHydrated(function (callable $set, $record) {
                                if ($record?->area?->departamento_id) {
                                    $set('departamento_id', $record->area->departamento_id);
                                }
                            })
                            ->afterStateUpdated(fn (callable $set) => $set('area_id', null))
                            ->columnSpan(1),

                        Select::make('area_id')
                            ->label('Área')
                            ->options(function (Forms\Get $get) {
                                $departamentoId = $get('departamento_id');

                                return \App\Models\Area::query()
                                    ->when($departamentoId, fn ($q) => $q->where('departamento_id', $departamentoId))
                                    ->pluck('descripcion', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disabled(fn (Forms\Get $get) => ! $get('departamento_id'))
                            ->helperText(fn (Forms\Get $get) =>
                                ! $get('departamento_id') ? 'Selecciona primero un departamento' : null
                            )
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Datos del cargo')
                    ->description('El nombre debe ser único dentro de la misma área.')
                    ->schema([
                        TextInput::make('descripcion')
                            ->label('Nombre del cargo')
                            ->placeholder('Ej: Supervisor, Auxiliar, Coordinador...')
                            ->required()
                            ->maxLength(250)
                            // ✅ Único por área, no global
                            ->unique(
                                table: 'cargos',
                                column: 'descripcion',
                                ignoreRecord: true,
                                modifyRuleUsing: fn ($rule, Forms\Get $get) =>
                                    $rule->where('area_id', $get('area_id'))
                            )
                            ->validationMessages([
                                'unique' => 'Ya existe un cargo con este nombre en el área seleccionada.',
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
                Tables\Columns\TextColumn::make('area.departamento.descripcion')
                    ->label('Departamento')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('area.descripcion')
                    ->label('Área')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Cargo')
                    ->weight('bold')
                    ->sortable()
                    ->searchable(),

                // ✅ Cuántos colaboradores tienen este cargo
                Tables\Columns\TextColumn::make('colaboradores_count')
                    ->label('Colaboradores')
                    ->counts('colaboradores')
                    ->badge()
                    ->color('success')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'descripcion')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(static::getModalWidth()),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->disabled(fn (Cargo $record) => $record->colaboradores()->exists())
                    ->tooltip(fn (Cargo $record) =>
                        $record->colaboradores()->exists()
                            ? 'No se puede eliminar: hay colaboradores con este cargo'
                            : null
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('descripcion', 'asc')
            ->groups([
                Tables\Grouping\Group::make('area.descripcion')
                    ->label('Área')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCargos::route('/'),
            'edit'  => Pages\EditCargo::route('/{record}/edit'),
        ];
    }
}
