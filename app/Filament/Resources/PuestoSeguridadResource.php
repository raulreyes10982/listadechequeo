<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuestoSeguridadResource\Pages;
use App\Filament\Resources\PuestoSeguridadResource\RelationManagers;
use App\Models\PuestoSeguridad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextColumn;

class PuestoSeguridadResource extends Resource
{
    protected static ?string $model = PuestoSeguridad::class;

    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Puestos de Seguridad';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    //->rule('unique:puesto_seguridads,codigo')
                    ->default(null),
                Forms\Components\TextInput::make('puesto')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    //->rule('unique:puesto_seguridads,puesto')
                    ->default(null),
                TimePicker::make('inicio_hora')
                    ->label('Hora de Inicio')
                    //->withoutSeconds()
                    ->seconds(false)
                    ->displayFormat('H:i') // 24 horas (por ejemplo: 14:30)
                    ->native(false),       // Usa el componente de Filament (no el nativo del navegador)
                TimePicker::make('fin_hora')
                    ->label('Hora de Fin')
                    //->withoutSeconds()
                    ->seconds(false)
                    ->displayFormat('H:i')
                    ->native(false),
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(250)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Codigo')
                    ->alignment('center')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('puesto')
                    ->label('Puesto')
                    ->alignment('center')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('horas_trabajadas')
                    ->label('Turno')
                    ->searchable(false)
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if (!$record->inicio_hora || !$record->fin_hora) {
                            return '—';
                        }

                        try {
                            $inicio = \Carbon\Carbon::parse($record->inicio_hora);
                            $fin = \Carbon\Carbon::parse($record->fin_hora);

                            // Si fin < inicio, asumimos que pasa al día siguiente
                            if ($fin->lessThan($inicio)) {
                                $fin->addDay();
                            }

                            $horas = $inicio->diffInMinutes($fin) / 60;

                            return number_format($horas) . ' horas';
                        } catch (\Exception $e) {
                            return 'Error';
                        }
                    }),
                Tables\Columns\TextColumn::make('inicio_hora')
                    ->label('Inicia')
                    ->alignment('center')
                    ->Time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fin_hora')
                    ->label('Finaliza')
                    ->alignment('center')
                    ->Time()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observacion')
                    ->alignment('center')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->alignment('center')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->alignment('center')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('editar')->modalWidth('lg'),
                Tables\Actions\DeleteAction::make()->label('eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPuestoSeguridads::route('/'),
            //'create' => Pages\CreatePuestoSeguridad::route('/create'),
           // 'edit' => Pages\EditPuestoSeguridad::route('/{record}/edit'),
        ];
    }
}
