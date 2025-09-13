<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrarTurnoResource\Pages;
use App\Models\RegistrarTurno;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;


class RegistrarTurnoResource extends Resource
{
    protected static ?string $model = RegistrarTurno::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Registrar Turno';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('puesto_seguridad_id')
                    ->label('Puesto de Seguridad')
                    ->relationship('puestoSeguridad', 'puesto') 
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->codigo} - {$record->puesto}")
                    ->searchable()
                    ->preload() //  esto ayuda a que cargue los registros inmediatamente
                    ->required(),

                Select::make('colaborador_id')
                    ->label('Colabordor')
                    ->relationship('colaborador', 'nombre')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre . ' ' . $record->apellido)
                    ->searchable()
                    ->required()
                    //->columnSpan(6)
                    ->preload(),

                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->default(now())
                    ->minDate(now()) // no deja elegir días anteriores
                    ->required(),
                    
                Forms\Components\TimePicker::make('hora_inicio')
                    ->label('Hora inicio')
                    ->seconds(false)
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false),

                Forms\Components\TimePicker::make('hora_fin')
                    ->label('Hora fin')
                    ->seconds(false)
                    ->required()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false),

                Forms\Components\Textarea::make('observacion')
                    ->label('Observación')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('puestoSeguridad')
                ->label('Puesto de Seguridad')
                ->getStateUsing(fn ($record) => "{$record->puestoSeguridad->codigo} - {$record->puestoSeguridad->puesto}")
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('colaborador')
                ->label('Colaborador')
                ->getStateUsing(fn ($record) => "{$record->colaborador->nombre} {$record->colaborador->apellido}")
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('fecha')
                ->label('Fecha')
                ->date('d/m/Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('hora_inicio')
                ->label('Hora inicio')
                ->time('H:i') // formato 24h
                ->sortable(),

            Tables\Columns\TextColumn::make('hora_fin')
                ->label('Hora fin')
                ->time('H:i') // formato 24h
                ->sortable(),

            Tables\Columns\TextColumn::make('observacion')
                ->label('Observación')
                ->limit(30)
                ->tooltip(fn ($record) => $record->observacion),
        ])
        ->filters([
            Tables\Filters\Filter::make('fecha')
                ->form([
                    Forms\Components\DatePicker::make('fecha'),
                ])
                ->query(fn (Builder $query, array $data) => 
                    $query->when(
                        $data['fecha'],
                        fn (Builder $query, $date) => $query->whereDate('fecha', $date),
                    )
                ),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrarTurnos::route('/'),
            'create' => Pages\CreateRegistrarTurno::route('/create'),
            'edit' => Pages\EditRegistrarTurno::route('/{record}/edit'),
        ];
    }
}
