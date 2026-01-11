<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificacionTurnoResource\Pages;
use App\Models\VerificacionTurno;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class VerificacionTurnoResource extends Resource
{
    protected static ?string $model = VerificacionTurno::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Verificación de Turnos';
    protected static ?int $navigationSort = 3; 

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('registrar_turno_id')
                ->relationship('turno', 'id')
                ->label('Turno Programado')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('tipo')
                ->options([
                    'ingreso' => 'Ingreso',
                    'ronda' => 'Ronda',
                    'salida' => 'Salida',
                    'reemplazo' => 'Reemplazo',
                ])
                ->label('Tipo de Verificación')
                ->required(),

            Forms\Components\DateTimePicker::make('hora_verificacion')
                ->label('Hora de Verificación')
                ->default(now())
                ->disabled(),

            Forms\Components\Textarea::make('observacion')
                ->label('Observación')
                ->rows(3),

            Forms\Components\Hidden::make('verificado_por')
                ->default(fn () => Auth::id())
                ->dehydrated(),

            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'verificado' => 'Verificado',
                    'cerrado' => 'Cerrado',
                ])
                ->label('Estado')
                ->default('pendiente'),
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
                Tables\Columns\TextColumn::make('turno.colaborador.nombre')
                    ->label('Colaborador')
                    ->formatStateUsing(function ($state, $record) {
                        // Muestra nombre y apellido juntos
                        if ($record->turno && $record->turno->colaborador) {
                            return $record->turno->colaborador->nombre . ' ' . $record->turno->colaborador->apellido;
                        }
                        return $state ?? 'N/A';
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('turno.puestoSeguridad.puesto')
                    ->label('Puesto')
                    ->formatStateUsing(function ($state, $record) {
                        // Muestra código - puesto
                        if ($record->turno && $record->turno->puestoSeguridad) {
                            $codigo = $record->turno->puestoSeguridad->codigo ?? '';
                            $puesto = $record->turno->puestoSeguridad->puesto ?? '';
                            
                            if (!empty($codigo) && !empty($puesto)) {
                                return $codigo . ' - ' . $puesto;
                            } elseif (!empty($puesto)) {
                                return $puesto;
                            }
                        }
                        return $state ?? 'N/A';
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn ($record) => match ($record->tipo) {
                        'ingreso' => 'info',
                        'ronda' => 'warning',
                        'salida' => 'success',
                        'reemplazo' => 'secondary',
                    })
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('hora_verificacion')
                    ->dateTime('d/m/Y H:i')
                    ->label('Hora'),

                Tables\Columns\TextColumn::make('verificador.name')
                    ->label('Verificado por'),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn ($record) => match ($record->estado) {
                        'pendiente' => 'warning',
                        'verificado' => 'success',
                        'cerrado' => 'danger',
                    })
                    ->label('Estado'),
            ])

            // Acción individual por registro
            ->actions([
                Action::make('scanQr')
                    ->label('Escanear QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->modalHeading('Escanear Código QR del Puesto')
                    ->modalWidth('lg')
                    ->modalContent(fn ($record) => view('filament.qr-scanner', [
                        'verificacionId' => $record->id,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            
            // Deshabilitar todas las acciones masivas
            ->bulkActions([])
            
            // Quitar el botón de crear cuando no hay registros
            ->emptyStateActions([]);
    }

    /*
    |--------------------------------------------------------------------------
    | PÁGINAS FILAMENT
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerificacionTurnos::route('/'),
            // Se eliminan las páginas de creación y edición
            // 'create' => Pages\CreateVerificacionTurno::route('/create'),
            // 'edit' => Pages\EditVerificacionTurno::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS PARA RESTRINGIR ACCIONES
    |--------------------------------------------------------------------------
    */
    public static function canCreate(): bool
    {
        // No permitir crear verificaciones manualmente
        return false;
    }

    public static function canEdit($record): bool
    {
        // No permitir editar verificaciones
        return false;
    }

    public static function canDelete($record): bool
    {
        // No permitir eliminar verificaciones
        return false;
    }
}