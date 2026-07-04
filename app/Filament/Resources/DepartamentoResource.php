<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartamentoResource\Pages;
use App\Filament\Resources\DepartamentoResource\RelationManagers\AreasRelationManager;
use App\Models\Departamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class DepartamentoResource extends Resource
{
    protected static ?string $model = Departamento::class;

    protected static ?string $navigationGroup = 'Organización';
    protected static ?string $navigationLabel = 'Departamentos';
    protected static ?string $navigationIcon  = 'heroicon-o-building-office';
    protected static ?int    $navigationSort  = 1;

    public static function getModalWidth(): MaxWidth
    {
        return MaxWidth::Large;
    }

    // ✅ Badge de navegación — muestra el total de departamentos
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
                Section::make('Datos del departamento')
                    ->description('El nombre debe ser único en todo el sistema.')
                    ->schema([
                        TextInput::make('descripcion')
                            ->label('Nombre del departamento')
                            ->placeholder('Ej: Administración, Mantenimiento, Seguridad...')
                            ->required()
                            ->maxLength(250)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Ya existe un departamento con este nombre.',
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
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Departamento')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                // ✅ Contador de áreas asociadas
                Tables\Columns\TextColumn::make('areas_count')
                    ->label('Áreas')
                    ->counts('areas')
                    ->badge()
                    ->color('info')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(static::getModalWidth()),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    // ✅ Evita borrar un departamento que aún tiene áreas
                    ->disabled(fn (Departamento $record) => $record->areas()->exists())
                    ->tooltip(fn (Departamento $record) =>
                        $record->areas()->exists()
                            ? 'No se puede eliminar: tiene áreas asociadas'
                            : null
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('descripcion', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            AreasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartamentos::route('/'),
            'edit'  => Pages\EditDepartamento::route('/{record}/edit'),
        ];
    }
}
