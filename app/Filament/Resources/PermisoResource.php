<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermisoResource\Pages;
use App\Filament\Resources\PermisoResource\RelationManagers\TrabajadoresRelationManager;
use App\Models\BitacoraEstado;
use App\Models\Estado;
use App\Models\Permiso;
use App\Models\TipoPermiso;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PermisoResource extends Resource
{
    protected static ?string $model           = Permiso::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Permisos';
    protected static ?string $navigationLabel = 'Permiso trabajo';
    protected static ?string $pluralLabel     = 'Permisos de trabajo';
    protected static ?string $label           = 'Permiso de trabajo';
    protected static ?int    $navigationSort  = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Hidden::make('subidopor'),
                // ── FILA 1: Autorizado + Inicio + Fin ────────────────────
                Grid::make(12)
                    ->schema([
                        Select::make('colaborador_id')
                            ->label('Autorizado *')
                            ->relationship('colaborador', 'nombre')
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                $record->nombre . ' ' . $record->apellido
                            )
                            ->searchable()
                            ->required()
                            ->preload()
                            ->columnSpan(6),

                        DatePicker::make('fecha_inicio_trabajo')
                            ->label('Inicio permiso *')
                            ->native(false)
                            ->displayFormat('d M, Y')
                            ->minDate(Carbon::today())
                            ->required()
                            ->columnSpan(3),

                        DatePicker::make('fecha_fin_trabajo')
                            ->label('Fin permiso *')
                            ->native(false)
                            ->displayFormat('d M, Y')
                            ->minDate(Carbon::today())
                            ->required()
                            ->afterOrEqual('fecha_inicio_trabajo')
                            ->columnSpan(3),
                    ]),

                // ── FILA 2: Tipo permiso + Unidad/Contratista ────────────
                Grid::make(12)
                    ->schema([
                        Select::make('tipo_permiso_id')
                            ->label('Permiso de trabajo')
                            ->relationship('tipoPermiso', 'descripcion')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->columnSpan(6)
                            ->afterStateUpdated(function (callable $set, $state) {
                                $permiso = TipoPermiso::find($state);
                                if (! $permiso) {
                                    $set('local_id', null);
                                    $set('contratistas_id', null);
                                    return;
                                }
                                match ($permiso->descripcion) {
                                    'Permiso tercero'                    => $set('local_id', null),
                                    'Permiso interno', 'Permiso externo' => $set('contratistas_id', null),
                                    default                              => null,
                                };
                            }),

                        // Unidad Privada
                        Select::make('local_id')
                            ->label('Unidad Privada')
                            ->relationship('local', 'descripcion')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->option_label)
                            ->searchable()
                            ->preload()
                            ->columnSpan(6)
                            ->visible(fn(Forms\Get $get) => in_array(
                                optional(TipoPermiso::find($get('tipo_permiso_id')))?->descripcion,
                                ['Permiso interno', 'Permiso externo']
                            )),

                        // Contratista
                        Select::make('contratistas_id')
                            ->label('Contratista')
                            ->relationship('contratistas', 'descripcion')
                            ->searchable()
                            ->preload()
                            ->columnSpan(6)
                            ->visible(
                                fn(Forms\Get $get) =>
                                optional(TipoPermiso::find($get('tipo_permiso_id')))?->descripcion === 'Permiso tercero'
                            ),
                    ]),

                // ── FILA 3: Tipo de actividad (checkboxes) ────────────────
                Section::make('Tipo de actividad')
                    ->schema([
                        CheckboxList::make('tipo_actividad')
                            ->label('')
                            ->options([
                                'Trabajo Altura'           => 'Trabajo Altura',
                                'Trabajo Caliente'         => 'Trabajo Caliente',
                                'Trabajo Confinado'        => 'Trabajo Confinado',
                                'Ingreso y salida equipos' => 'Ingreso y salida equipos',
                                'Trabajo Electrico'        => 'Trabajo Electrico',
                                'Arreglos internos'        => 'Arreglos internos',
                                'Reuniones, inventarios'   => 'Reuniones, inventarios',
                                'Otros'                    => 'Otros',
                            ])
                            ->columns(4)
                            ->gridDirection('row')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->compact(),

                // ── FILA 4: Actividad + Observaciones + PDF ──────────────
                Grid::make(3)
                    ->schema([
                        Textarea::make('actividad')
                            ->label('Actividad a realizar')
                            ->maxLength(500)
                            ->rows(4)
                            ->default(null),

                        Textarea::make('descripcion')
                            ->label('Observaciones')
                            ->maxLength(500)
                            ->rows(4)
                            ->default(null),

                        FileUpload::make('archivo_pdf')
                            ->label('Subir PDF *')
                            ->disk('public')
                            ->directory('permisos')
                            ->acceptedFileTypes(['application/pdf'])
                            ->getUploadedFileNameForStorageUsing(function ($file) {
                                $ultimo = Permiso::max('id') + 1;
                                return 'permiso_externo_' .
                                    str_pad($ultimo, 5, '0', STR_PAD_LEFT) . '.pdf';
                            })
                            ->required(),
                    ]),

                // ── SECCIÓN: Trabajadores ─────────────────────────────────
                Section::make('Trabajadores')
                    ->schema([
                        Repeater::make('trabajadores')
                            ->label('')
                            ->relationship()
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre completo *')
                                    ->required()
                                    ->maxLength(150),

                                TextInput::make('documento')
                                    ->label('Documento *')
                                    ->required()
                                    ->maxLength(50),
                            ])
                            ->columns(2)
                            ->addActionLabel('+ Agregar trabajador')
                            ->defaultItems(1)
                            ->columnSpanFull()
                            ->reorderableWithButtons()
                            ->deletable()
                            ->itemLabel(fn() => null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subidopor')
                    ->label('Subido por')
                    ->alignment('center')
                    ->sortable()->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tipoPermiso.descripcion')
                    ->label('Tipo permiso')
                    ->badge()->color('info')
                    ->alignment('center')
                    ->sortable()->searchable(),

                TextColumn::make('contratista_o_unidad')
                    ->label('Contratista / Unidad')
                    ->alignment('center')
                    ->getStateUsing(
                        fn($record) =>
                        $record->contratistas?->descripcion
                            ?? $record->local?->option_label
                            ?? '—'
                    ),

                TextColumn::make('rango_fechas')
                    ->label('Vigencia')
                    ->alignment('center')
                    ->getStateUsing(
                        fn($record) =>
                        Carbon::parse($record->fecha_inicio_trabajo)->format('d/m/Y') .
                            ' → ' .
                            Carbon::parse($record->fecha_fin_trabajo)->format('d/m/Y')
                    )
                    ->badge()
                    ->color(
                        fn($record) =>
                        Carbon::parse($record->fecha_fin_trabajo)->isFuture()
                            ? 'success' : 'danger'
                    ),

                TextColumn::make('trabajadores_count')
                    ->label('Trabajadores')
                    ->counts('trabajadores')
                    ->badge()->color('gray')
                    ->alignment('center'),

                IconColumn::make('archivo_pdf')
                    ->label('PDF')
                    ->icon(
                        fn($record) =>
                        $record->archivo_pdf
                            ? 'heroicon-o-document-text'
                            : 'heroicon-o-x-circle'
                    )
                    ->color(
                        fn($record) =>
                        $record->archivo_pdf ? 'success' : 'danger'
                    )
                    ->url(
                        fn($record) =>
                        $record->archivo_pdf
                            ? asset('storage/' . $record->archivo_pdf)
                            : null,
                        true
                    )
                    ->tooltip(
                        fn($record) =>
                        $record->archivo_pdf ? 'Ver PDF' : 'Sin archivo'
                    )
                    ->alignment('center'),

                TextColumn::make('created_at')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_permiso_id')
                    ->label('Tipo de permiso')
                    ->relationship('tipoPermiso', 'descripcion')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('local_id')
                    ->label('Unidad')
                    ->options(
                        \App\Models\Local::all()
                            ->pluck('option_label', 'id')
                    ),

                Tables\Filters\SelectFilter::make('contratistas_id')
                    ->label('Contratista')
                    ->relationship('contratistas', 'descripcion')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['desde'],
                                fn($query, $date) => $query->whereDate('fecha_inicio_trabajo', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn($query, $date) => $query->whereDate('fecha_fin_trabajo', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('estado')
                    ->label('Estado')
                    ->form([
                        Forms\Components\Select::make('estado')
                            ->options([
                                'vigente' => 'Vigente',
                                'vencido' => 'Vencido',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['estado'] ?? null) {
                            'vigente' => $query->whereDate('fecha_fin_trabajo', '>=', now()),
                            'vencido' => $query->whereDate('fecha_fin_trabajo', '<', now()),
                            default => $query,
                        };
                    }),

                Tables\Filters\TernaryFilter::make('archivo_pdf')
                    ->label('Tiene PDF'),
            ])
            ->actions([
                // ✅ Editar en modal — idéntico al crear
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->modalHeading('Editar Permiso de trabajo'),

                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermisos::route('/'),
        ];
    }
}
