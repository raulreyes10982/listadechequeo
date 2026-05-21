<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrarTurnoResource\Pages;
use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegistrarTurnoResource extends Resource
{
    protected static ?string $model = RegistrarTurno::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Registrar Turno';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([

                // Puesto de seguridad
                Select::make('puesto_seguridad_id')
                    ->label('Puesto de Seguridad')
                    ->relationship('puestoSeguridad', 'puesto')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => "{$record->codigo} - {$record->puesto}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(3),

                // Colaborador
                Select::make('colaborador_id')
                    ->label('Colaborador')
                    ->relationship('colaborador', 'nombre')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => "{$record->nombre} {$record->apellido}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(3),

                // Fecha del turno
                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->format('Y-m-d')
                    ->native(false)
                    ->default(Carbon::now()->format('Y-m-d'))
                    ->required()
                    ->columnSpan(2),

                // Hora inicio
                TimePicker::make('hora_inicio')
                    ->label('Hora inicio turno')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false)
                    ->minutesStep(1)
                    ->columnSpan(2),

                // Hora fin
                TimePicker::make('hora_fin')
                    ->label('Hora finaliza turno')
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false)
                    ->minutesStep(1)
                    ->columnSpan(2),

                // Observaciones
                Textarea::make('observacion')
                    ->label('Observación')
                    ->rows(4)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('puestoSeguridad')
                    ->label('Puesto de Seguridad')
                    ->getStateUsing(
                        fn ($record) => "{$record->puestoSeguridad->codigo} - {$record->puestoSeguridad->puesto}"
                    )
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('colaborador')
                    ->label('Colaborador')
                    ->getStateUsing(
                        fn ($record) => "{$record->colaborador->nombre} {$record->colaborador->apellido}"
                    )
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Hora inicio')
                    ->time('H:i')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('hora_fin')
                    ->label('Hora fin')
                    ->time('H:i')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('observacion')
                    ->label('Observación')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->observacion)
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        DatePicker::make('fecha'),
                    ])
                    ->query(
                        fn (Builder $query, array $data) =>
                            $query->when(
                                $data['fecha'],
                                fn (Builder $query, $date) =>
                                    $query->whereDate('fecha', $date)
                            )
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'),
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
