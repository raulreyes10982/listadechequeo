<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteResource\Pages;
use App\Models\BitacoraEstado;
use App\Models\Estado;
use App\Models\Reporte;
use App\Models\Zona;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReporteResource extends Resource
{
    protected static ?string $model          = Reporte::class;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int    $navigationSort  = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('subidopor'),
                Hidden::make('hora')->default(fn () => Carbon::now()->format('H:i')),
                Hidden::make('fecha')->default(fn () => Carbon::now()->format('Y-m-d')),

                // ── Encabezado con ícono ──────────────────────────────────
                Section::make()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('header')
                                    ->label('')
                                    ->content('')
                                    ->extraAttributes(['class' => 'hidden']),
                            ]),
                    ])
                    ->heading('Crear reporte')
                    ->description('Completa la información para generar un nuevo reporte.')
                    ->icon('heroicon-o-document-text')
                    ->compact(),

                // ── FILA 1: Categoría + Tipo + Prioridad ──────────────────
                Grid::make(3)
                    ->schema([
                        Select::make('categoria_reporte_id')
                            ->label('Categoría *')
                            ->relationship('categoria', 'descripcion')
                            ->preload()->searchable()->required()
                            ->placeholder('Seleccione una opción'),

                        Select::make('tipo_reporte_id')
                            ->label('Tipo de Reporte *')
                            ->relationship('tipoReporte', 'descripcion')
                            ->preload()->searchable()->required()
                            ->placeholder('Seleccione una opción'),

                        Select::make('prioridad_id')
                            ->label('Prioridad *')
                            ->relationship('prioridad', 'descripcion')
                            ->preload()->searchable()->required()
                            ->placeholder('Seleccione una opción'),
                    ]),

                // ── FILA 2: Zona + Ubicación/Unidad ──────────────────────
                Grid::make(2)
                    ->schema([
                        Select::make('zona_id')
                            ->label('Zona *')
                            ->relationship('zona', 'descripcion')
                            ->preload()->searchable()->required()
                            ->placeholder('Seleccione una opción')
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $zona = Zona::find($state);
                                if (! $zona || $zona->descripcion !== 'Zona Privada') {
                                    $set('local_id', null);
                                } else {
                                    $set('ubicacion_id', null);
                                }
                            }),

                        // Ubicación normal
                        Select::make('ubicacion_id')
                            ->label('Ubicación *')
                            ->relationship('ubicacion', 'descripcion')
                            ->preload()->searchable()->required()
                            ->placeholder('Seleccione una opción')
                            ->visible(fn (Forms\Get $get) =>
                                optional(Zona::find($get('zona_id')))->descripcion !== 'Zona Privada'
                            ),

                        // Unidad privada
                        Select::make('local_id')
                            ->label('Unidad Privada *')
                            ->relationship('local', 'descripcion')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->option_label)
                            ->searchable()->preload()
                            ->placeholder('Seleccione una unidad')
                            ->visible(fn (Forms\Get $get) =>
                                optional(Zona::find($get('zona_id')))->descripcion === 'Zona Privada'
                            ),
                    ]),

                // ── FILA 3: Descripción + Imágenes ───────────────────────
                Grid::make(2)
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Describe el incidente o situación reportada...')
                            ->maxLength(1000)
                            ->rows(6),

                        FileUpload::make('imagenes')
                            ->label('Imágenes')
                            ->directory('reportes')
                            ->disk('public')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->helperText('Formatos permitidos: JPG, PNG, PDF (Máx. 10 MB)')
                            ->panelLayout('grid'),
                    ]),

                // ── Estado (solo en edición) ──────────────────────────────
                Select::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'descripcion')
                    ->preload()->searchable()
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
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/M/Y')
                    ->searchable()->sortable()->alignment('center'),

                Tables\Columns\TextColumn::make('hora')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable()->alignment('center'),

                Tables\Columns\TextColumn::make('categoria.descripcion')
                    ->label('Reporte')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('tipoReporte.descripcion')
                    ->label('Tipo de Reporte')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('prioridad.descripcion')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'alta', 'urgente', 'crítica', 'critica' => 'danger',
                        'media'  => 'warning',
                        'baja'   => 'info',
                        default  => 'gray',
                    })
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('zona.descripcion')
                    ->label('Zona')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('ubicacion_y_unidad')
                    ->label('Ubicación')
                    ->getStateUsing(fn ($record) =>
                        ($record->ubicacion?->descripcion ?? '') .
                        ($record->local ? ' ' . $record->local->option_label : '')
                    )
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('estado.descripcion')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pendiente'  => 'danger',
                        'En proceso' => 'warning',
                        'Finalizado', 'Cerrado' => 'success',
                        default      => 'gray',
                    })
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar')
                        ->modalWidth(MaxWidth::ThreeExtraLarge),

                    Tables\Actions\DeleteAction::make()
                        ->label('Eliminar'),

                    // ✅ Cambiar estado con permiso Spatie
                    Action::make('cambiarEstadoBitacora')
                        ->label('Cambiar estado')
                        ->modalWidth('lg')
                        ->icon('heroicon-o-arrow-path')
                        ->visible(function () {
                            if (! Auth::check()) return false;
                            try {
                                return Auth::user()->hasPermissionTo('cambiar_estado');
                            } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                                return false;
                            }
                        })
                        ->form([
                            Select::make('estado_id')
                                ->label('Nuevo estado')
                                ->options(Estado::pluck('descripcion', 'id')->toArray())
                                ->required(),

                            Textarea::make('descripcion')
                                ->label('Observaciones')
                                ->rows(4)
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            $estadoAnterior  = $record->estado->descripcion ?? 'Sin estado';
                            $nuevoEstado     = Estado::find($data['estado_id']);
                            $nuevoEstadoDesc = $nuevoEstado->descripcion ?? 'Desconocido';

                            $record->update(['estado_id' => $data['estado_id']]);

                            BitacoraEstado::create([
                                'reporte_id'   => $record->id,
                                'estado_id'    => $data['estado_id'],
                                'descripcion'  => "Cambio: {$estadoAnterior} → {$nuevoEstadoDesc}. " . $data['descripcion'],
                                'cambiado_por' => Auth::user()->name ?? 'Sistema',
                                'fecha'        => Carbon::now()->format('Y-m-d'),
                                'hora'         => Carbon::now()->format('H:i:s'),
                            ]);

                            Notification::make()
                                ->title('Estado actualizado')
                                ->body("De «{$estadoAnterior}» a «{$nuevoEstadoDesc}»")
                                ->success()
                                ->send();
                        })
                        ->after(fn ($livewire) => $livewire->dispatch('$refresh')),
                ])
                ->button()
                ->label('Acciones'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportes::route('/'),
        ];
    }
}
