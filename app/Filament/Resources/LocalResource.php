<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocalResource\Pages;
use App\Models\Local;
use App\Models\Nomenclatura;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LocalResource extends Resource
{
    protected static ?string $model = Local::class;

    protected static ?string $navigationLabel = 'Unidades Comerciales';
    protected static ?string $pluralLabel     = 'Unidades Comerciales';
    protected static ?string $label           = 'Unidad Comercial';
    protected static ?string $navigationGroup = 'Localización';
    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?int    $navigationSort  = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::activos()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO — crear y editar (solo datos básicos, sin renombrado mágico)
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del local')
                    ->description(
                        '💡 Si el arrendatario cambió, primero desactiva el local actual desde ' .
                        'la tabla y luego crea uno nuevo con el nuevo nombre en la misma ubicación.'
                    )
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Local')
                            ->placeholder('Ej: Studio F, Zapatillas Piel, Café Express...')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),

                        // ✅ Solo nomenclaturas activas disponibles
                        Select::make('nomenclatura_id')
                            ->label('Ubicación (código de nomenclatura)')
                            ->options(
                                Nomenclatura::activos()
                                    ->with('categoriaLocal')
                                    ->get()
                                    ->mapWithKeys(fn ($n) => [
                                        $n->id =>
                                            ($n->categoriaLocal->descripcion ?? '') .
                                            ' — ' . $n->codigo .
                                            ($n->modulo ? " ({$n->modulo})" : '') .
                                            ' Piso ' . ($n->piso ?? '?'),
                                    ])
                            )
                            ->searchable()
                            ->required()
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
                TextColumn::make('nomenclatura.categoriaLocal.descripcion')
                    ->label('Unidad')
                    ->badge()
                    ->color('gray')
                    ->alignment('center')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nomenclatura.codigo')
                    ->label('Código')
                    ->alignment('center')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Nombre del local')
                    ->alignment('center')
                    ->sortable()
                    ->searchable()
                    // ✅ Tachado visual si está inactivo
                    ->formatStateUsing(fn ($state, Local $record) =>
                        $record->activo ? $state : "〔Inactivo〕 {$state}"
                    )
                    ->color(fn (Local $record) => $record->activo ? null : 'gray'),

                TextColumn::make('nomenclatura.piso')
                    ->label('Piso')
                    ->alignment('center')
                    ->sortable(),

                TextColumn::make('nomenclatura.modulo')
                    ->label('Bloque')
                    ->alignment('center')
                    ->sortable(),

                // ✅ Ícono de estado claro
                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignment('center'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                // ✅ Por defecto solo activos — más limpio para el día a día
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos (historial)')
                    ->placeholder('Todos')
                    ->default(true),

                Tables\Filters\SelectFilter::make('nomenclatura_id')
                    ->label('Código de ubicación')
                    ->relationship('nomenclatura', 'codigo')
                    ->searchable()
                    ->preload(),
            ])

            ->actions([
                // ✅ Editar — solo para correcciones menores (sin cambio de arrendatario)
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(MaxWidth::Large)
                    ->modalDescription('Usa este botón solo para corregir datos. Si el arrendatario cambió, desactiva este local y crea uno nuevo.')
                    // Solo para locales ACTIVOS
                    ->visible(fn (Local $record) => $record->activo),

                // ✅ DESACTIVAR — cuando se va el arrendatario
                Tables\Actions\Action::make('desactivar')
                    ->label('Desactivar')
                    ->icon('heroicon-m-eye-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Desactivar este local?')
                    ->modalDescription(fn (Local $record) =>
                        "El local \"{$record->nombre}\" ({$record->nomenclatura?->codigo}) " .
                        "quedará inactivo. El historial de permisos y reportes asociados se conserva. " .
                        "Puedes crear un nuevo local en la misma ubicación con el nuevo arrendatario."
                    )
                    ->modalSubmitActionLabel('Sí, desactivar')
                    ->action(function (Local $record) {
                        $record->update(['activo' => false]);

                        Notification::make()
                            ->title('Local desactivado')
                            ->body(
                                "\"{$record->nombre}\" fue desactivado. " .
                                "Ahora puedes crear un nuevo local en la misma ubicación."
                            )
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Local $record) => $record->activo),

                // ✅ ACTIVAR — para reactivar un local inactivo
                Tables\Actions\Action::make('activar')
                    ->label('Activar')
                    ->icon('heroicon-m-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Reactivar este local?')
                    ->modalDescription(fn (Local $record) =>
                        "El local \"{$record->nombre}\" volverá a estar disponible."
                    )
                    ->action(fn (Local $record) => $record->update(['activo' => true]))
                    ->visible(fn (Local $record) => ! $record->activo),
            ])

            ->bulkActions([
                // ✅ Desactivación masiva — para cuando remodelan y cierran varios locales
                Tables\Actions\BulkAction::make('desactivarSeleccionados')
                    ->label('Desactivar seleccionados')
                    ->icon('heroicon-m-eye-slash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Desactivar los locales seleccionados?')
                    ->modalDescription('Todos quedarán inactivos pero su historial se conserva.')
                    ->action(fn ($records) => $records->each->update(['activo' => false]))
                    ->deselectRecordsAfterCompletion(),
            ])

            ->defaultSort('nombre', 'asc')
            ->emptyStateHeading('No hay locales activos')
            ->emptyStateDescription('Usa el botón "Nuevo" para registrar una unidad comercial.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocals::route('/'),
        ];
    }
}
