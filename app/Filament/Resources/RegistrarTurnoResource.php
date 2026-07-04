<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrarTurnoResource\Pages;
use App\Models\RegistrarTurno;
use App\Rules\SinSolapamientoTurno;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class RegistrarTurnoResource extends Resource
{
    protected static ?string $model = RegistrarTurno::class;

    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Registrar Turno';
    protected static ?int    $navigationSort  = 2;

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO con validación reactiva de solapamiento
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([

                Select::make('puesto_seguridad_id')
                    ->label('Puesto de Seguridad')
                    ->relationship('puestoSeguridad', 'puesto')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->puesto}")
                    ->searchable()->preload()->required()
                    ->columnSpan(3)
                    ->reactive(),

                Select::make('colaborador_id')
                    ->label('Colaborador')
                    ->relationship('colaborador', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombre} {$record->apellido}")
                    ->searchable()->preload()->required()
                    ->columnSpan(3)
                    ->reactive(),

                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->format('Y-m-d')
                    ->native(false)
                    ->default(Carbon::now()->format('Y-m-d'))
                    ->required()
                    ->columnSpan(2)
                    ->reactive(), // ✅ reactive

                TimePicker::make('hora_inicio')
                    ->label('Hora inicio turno')
                    ->required()->seconds(false)
                    ->displayFormat('H:i')->native(false)->minutesStep(1)
                    ->columnSpan(2)
                    ->reactive()
                    // ✅ Validación en tiempo real al salir del campo
                    ->rules(fn ($get) => [
                        new SinSolapamientoTurno(
                            fecha:         $get('fecha'),
                            horaInicio:    $get('hora_inicio'),
                            horaFin:       $get('hora_fin'),
                            puestoId:      $get('puesto_seguridad_id'),
                            colaboradorId: $get('colaborador_id'),
                            excludeId:     $get('id') ? (int) $get('id') : null,
                        ),
                    ]),

                TimePicker::make('hora_fin')
                    ->label('Hora finaliza turno')
                    ->required()->seconds(false)
                    ->displayFormat('H:i')->native(false)->minutesStep(1)
                    ->columnSpan(2)
                    ->reactive()
                    // ✅ Misma validación en hora_fin para cubrir cambios desde ahí
                    ->rules(fn ($get) => [
                        new SinSolapamientoTurno(
                            fecha:         $get('fecha'),
                            horaInicio:    $get('hora_inicio'),
                            horaFin:       $get('hora_fin'),
                            puestoId:      $get('puesto_seguridad_id'),
                            colaboradorId: $get('colaborador_id'),
                            excludeId:     $get('id') ? (int) $get('id') : null,
                        ),
                    ]),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->rows(4)->nullable()->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('puestoSeguridad')
                    ->label('Puesto de Seguridad')
                    ->getStateUsing(fn ($record) =>
                        ($record->puestoSeguridad->codigo ?? '—') . ' - ' . ($record->puestoSeguridad->puesto ?? '—')
                    )
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('colaborador')
                    ->label('Colaborador')
                    ->getStateUsing(fn ($record) =>
                        trim(($record->colaborador->nombre ?? '') . ' ' . ($record->colaborador->apellido ?? ''))
                    )
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')->date('d/m/Y')
                    ->sortable()->alignment('center'),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora inicio')->time('H:i')
                    ->sortable()->alignment('center'),

                Tables\Columns\TextColumn::make('hora_fin')
                    ->label('Hora fin')->time('H:i')
                    ->sortable()->alignment('center'),

                Tables\Columns\TextColumn::make('observacion')
                    ->label('Observación')->limit(30)
                    ->tooltip(fn ($record) => $record->observacion)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\Filter::make('fecha')
                    ->form([DatePicker::make('fecha')])
                    ->query(fn (Builder $q, array $data) =>
                        $q->when($data['fecha'], fn ($q, $d) => $q->whereDate('fecha', $d))
                    ),
            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->using(function ($record, array $data) {
                        try {
                            $record->update($data);
                            return $record;
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            $mensaje = collect($e->errors())->flatten()->first()
                                ?? 'No se puede guardar el turno por un conflicto de horario.';

                            \Filament\Notifications\Notification::make()
                                ->title('⚠️ Conflicto de horario')
                                ->body($mensaje)
                                ->danger()
                                ->persistent()
                                ->send();

                            // Lanzar de nuevo para que Filament no cierre el modal
                            throw $e;
                        }
                    }),

                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrarTurnos::route('/'),
        ];
    }
}
