<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteResource\Pages;
use App\Models\Reporte;
use App\Models\Zona;
use App\Models\Estado;
use App\Models\BitacoraEstado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReporteResource extends Resource
{
    protected static ?string $model = Reporte::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([ 
                Forms\Components\Hidden::make('subidopor'),

                Forms\Components\TimePicker::make('hora')
                    ->label('Hora')
                    ->format('H:i')
                    ->default(Carbon::now()->format('H:i'))
                    ->hidden(),

                Forms\Components\DatePicker::make('fecha')
                    ->label('Fecha')
                    ->format('Y-m-d')
                    ->default(Carbon::now()->format('Y-m-d'))
                    ->hidden(),

                Forms\Components\Select::make('categoria_reporte_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('tipo_reporte_id')
                    ->label('Tipo de Reporte')
                    ->relationship('tipoReporte', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('prioridad_id')
                    ->relationship('prioridad', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(2),

                Select::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(3)
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $zona = Zona::find($state);
                        if (!$zona || $zona->descripcion !== 'Zona Privada') {
                            $set('local_id', null);
                        } else {
                            $set('ubicacion_id', null);
                        }
                    }),

                Select::make('ubicacion_id')
                    ->label('Ubicación')
                    ->relationship('ubicacion', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->columnSpan(3)
                    ->visible(fn (callable $get) => optional(Zona::find($get('zona_id')))->descripcion !== 'Zona Privada'),

                Select::make('local_id')
                    ->label('Unidad Privada')
                    ->relationship('local', 'descripcion')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->option_label)
                    ->searchable()
                    ->columnSpan(3)
                    ->preload()
                    ->visible(fn (callable $get) => optional(Zona::find($get('zona_id')))->descripcion === 'Zona Privada'),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->columnSpan(5),

                FileUpload::make('imagenes')
                    ->label('Imágenes')
                    ->directory('reportes')
                    ->disk('public')
                    ->image()
                    ->multiple()
                    ->maxFiles(3)
                    ->columnSpan(1),

                Forms\Components\Select::make('estado_id')
                    ->relationship('estado', 'descripcion')
                    ->preload()
                    ->searchable()
                    ->columnSpan(3)
                    ->visibleOn(['edit', 'view'])
                    ->disabled(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subidopor')
                    ->label('Subido por')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->dateTime('H:i')
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('categoria.descripcion')
                    ->label('Reporte')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipoReporte.descripcion')
                    ->label('Tipo de Reporte')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('prioridad.descripcion')
                    ->label('Prioridad')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('zona.descripcion')
                    ->label('Zona')
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('ubicacion_y_unidad')
                    ->label('Ubicación')
                    ->getStateUsing(function ($record) {
                        $ubicacion = $record->ubicacion?->descripcion ?? '';
                        $unidadPrivada = $record->local?->option_label;
                        return $unidadPrivada ? "{$ubicacion} {$unidadPrivada}" : $ubicacion;
                    })
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'   => 'danger',
                        'En proceso'  => 'warning',
                        'verificado'  => 'info',
                        'Finalizado'  => 'success',
                        default       => 'gray',
                    })
                    ->sortable()
                    ->searchable()
                    ->alignment('center'),
                
                Tables\Columns\IconColumn::make('imagenes')
                    ->label('Imágenes')
                    ->getStateUsing(fn ($record) => !empty($record->imagenes))
                    ->boolean()
                    ->trueIcon('heroicon-o-camera')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Observaciones')
                    ->wrap()
                    ->alignment('center')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar')
                        ->modalWidth('3xl'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar'),

                    // ACCIÓN CON LA SINTAXIS CORRECTA
                    Action::make('cambiarEstadoBitacora')
                        ->label('Cambiar estado')
                        ->modalWidth('lg')
                        ->icon('heroicon-o-document-text')
                        ->form([
                            Select::make('estado_id')
                                ->label('Nuevo estado')
                                ->options(Estado::all()->pluck('descripcion', 'id'))
                                ->required(),
                            Textarea::make('descripcion')
                                ->label('Observaciones del cambio')
                                ->rows(4)
                                ->required(),
                        ])
                        ->action(function ($data, $record) {
                            // LOG para depuración
                            \Illuminate\Support\Facades\Log::info('=== INICIANDO CAMBIO DE ESTADO ===');
                            \Illuminate\Support\Facades\Log::info('Reporte ID: ' . $record->id);
                            \Illuminate\Support\Facades\Log::info('Estado actual ID: ' . $record->estado_id);
                            \Illuminate\Support\Facades\Log::info('Nuevo estado ID: ' . $data['estado_id']);
                            
                            // Guardar estado anterior
                            $estadoAnterior = $record->estado->descripcion ?? 'Sin estado';
                            
                            // 1. Actualizar el estado del reporte
                            $record->estado_id = $data['estado_id'];
                            $guardado = $record->save();
                            
                            \Illuminate\Support\Facades\Log::info('¿Guardado?: ' . ($guardado ? 'SÍ' : 'NO'));
                            
                            if ($guardado) {
                                // Recargar el modelo
                                $record->refresh();
                                \Illuminate\Support\Facades\Log::info('Estado después de refresh: ' . $record->estado_id);
                                
                                // 2. Obtener nuevo estado
                                $nuevoEstado = Estado::find($data['estado_id']);
                                $nuevoEstadoDesc = $nuevoEstado->descripcion ?? 'Desconocido';
                                
                                // 3. Crear registro en bitácora
                                BitacoraEstado::create([
                                    'reporte_id'    => $record->id,
                                    'estado_id'     => $data['estado_id'],
                                    'descripcion'   => "Cambio de estado: {$estadoAnterior} → {$nuevoEstadoDesc}. " . 
                                                      $data['descripcion'],
                                    'cambiado_por'  => Auth::user()->name ?? 'Sistema',
                                    'fecha'         => Carbon::now()->format('Y-m-d'),
                                    'hora'          => Carbon::now()->format('H:i:s'),
                                ]);

                                \Illuminate\Support\Facades\Log::info('Bitácora creada para reporte: ' . $record->id);
                                
                                Notification::make()
                                    ->title('¡Estado actualizado!')
                                    ->body("El estado ha sido cambiado de {$estadoAnterior} a {$nuevoEstadoDesc}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->body('No se pudo actualizar el estado del reporte')
                                    ->danger()
                                    ->send();
                            }
                            
                            \Illuminate\Support\Facades\Log::info('=== FIN CAMBIO DE ESTADO ===');
                        })
                        ->after(function () {
                            // Esto fuerza un refresh de la página
                            redirect()->to(ReporteResource::getUrl('index'));
                        }),
                ])
                ->button()
                ->label('Acciones'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportes::route('/'),
        ];
    }
}