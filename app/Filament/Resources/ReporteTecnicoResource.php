<?php

namespace App\Filament\Resources;

use App\Models\ReporteTecnico;
use App\Models\EstadoReporte;
use App\Models\HistorialEstadoReporte;
use App\Filament\Resources\ReporteTecnicoResource\Pages;
use App\Filament\Resources\ReporteTecnicoResource\RelationManagers\HistorialEstadoReportesRelationManager;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Hidden, TimePicker, DatePicker, Select, Textarea};
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\{ActionGroup, Action};
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\MaxWidth;

class ReporteTecnicoResource extends Resource
{
    
    protected static ?string $model = ReporteTecnico::class;

    protected static ?string $navigationGroup = 'Equipos';
    protected static ?string $navigationLabel = 'Reporte Técnico';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Reporte Técnico';
    protected static ?string $pluralModelLabel = 'Reportes Técnicos';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('subidopor'),

            TimePicker::make('hora')
                ->label('Hora')
                ->format('H:i')
                ->default(Carbon::now()->format('H:i'))
                ->hidden(),

            DatePicker::make('fecha')
                ->label('Fecha')
                ->format('Y-m-d')
                ->default(Carbon::now()->format('Y-m-d'))
                ->hidden(),

            Select::make('equipo_id')
                ->label('Equipo')
                ->relationship('equipo', 'descripcion')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('tipo_intervencion_id')
                ->label('Tipo de Intervención')
                ->relationship('tipoIntervencion', 'nombre')
                ->searchable()
                ->preload()
                ->required(),

            Textarea::make('descripcion')
                ->label('Observaciones')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subidopor')
                    ->label('Subido por')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/M/Y')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('hora')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable()
                    ->searchable()
                    ->alignCenter(),

                TextColumn::make('equipo.descripcion')
                    ->label('Equipo')
                    ->sortable()
                    ->wrap()
                    ->searchable()
                    ->alignment('center'),

                TextColumn::make('tipoIntervencion.nombre')
                    ->label('Tipo Intervención')
                    ->sortable()
                    ->wrap()
                    ->searchable()
                    ->alignment('center'),

                TextColumn::make('ultimoEstado.estadoReporte.nombre')
                    ->label('Último Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'   => 'warning',
                        'En proceso'  => 'info',
                        'Finalizado'  => 'success',
                        'Cancelado'   => 'danger',
                        default       => 'gray',
                    })
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap()
                    ->alignment('center')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar')
                        ->modalWidth('3xl'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar'),

                    Action::make('cambiarEstado')
                        ->label('Cambiar estado')
                        ->modalWidth('lg')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('estado_reporte_id')
                                ->label('Nuevo estado')
                                ->options(
                                    EstadoReporte::pluck('nombre', 'id')->toArray()
                                )
                                ->required(),

                            Textarea::make('descripcion')
                                ->label('Observaciones')
                                ->rows(4)
                                ->required(),
                        ])
                        ->action(function (array $data, Action $action) {
                            $reporte = $action->getRecord();

                            HistorialEstadoReporte::create([
                                'reporte_tecnico_id' => $reporte->id,
                                'estado_reporte_id'  => $data['estado_reporte_id'],
                                'descripcion'        => $data['descripcion'],
                                'cambiado_por'       => Auth::user()->name ?? 'Sistema',
                            ]);

                            Notification::make()
                                ->title('Estado actualizado')
                                ->body('El estado del reporte fue cambiado correctamente.')
                                ->success()
                                ->send();
                        }),
                ])
                ->button()
                ->label('Acciones'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('equipo_id')
                ->label('Equipo')
                ->relationship('equipo', 'descripcion')
                    ->searchable()
    ->preload(),

                Tables\Filters\SelectFilter::make('tipo_intervencion_id')
                    ->label('Tipo Intervención')
                    ->relationship('tipoIntervencion', 'nombre')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('ultimoEstado.estadoReporte.nombre')
                    ->label('Estado')
                    ->relationship('ultimoEstado.estadoReporte', 'nombre')
                    ->preload()
                    ->searchable(),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn($q, $date) => $q->whereDate('fecha', '>=', $date))
                            ->when($data['hasta'], fn($q, $date) => $q->whereDate('fecha', '<=', $date));
                    }),
                ],
                layout: FiltersLayout::Modal // <-- mostrarlos como modal
            )
            ->filtersFormWidth('lg') // ancho del modal
            ->filtersFormMaxHeight('70vh') // alto máximo con scroll interno

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HistorialEstadoReportesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReporteTecnicos::route('/'),
        ];
    }
}

