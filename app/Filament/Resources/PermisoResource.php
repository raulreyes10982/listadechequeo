<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermisoResource\Pages;
use App\Models\Permiso;
use App\Models\TipoPermiso;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermisoResource extends Resource
{
    protected static ?string $model = Permiso::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Permisos';
    protected static ?string $navigationLabel = 'Permiso trabajo';
    protected static ?string $pluralLabel     = 'Permisos de trabajo';
    protected static ?string $label           = 'Permiso de trabajo';
    protected static ?int $navigationSort     = 2;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('subidopor'),

            Select::make('colaborador_id')
                ->label('Autorizado')
                ->relationship('colaborador', 'nombre')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->nombre . ' ' . $record->apellido)
                ->searchable()
                ->required()
                ->columnSpan(12)
                ->preload(),

            DatePicker::make('fecha_inicio_trabajo')
                ->label('Inicio permiso')
                ->native(false)
                ->minDate(Carbon::today())
                ->columnSpan(6)
                ->required(),

            DatePicker::make('fecha_fin_trabajo')
                ->label('Fin permiso')
                ->native(false)
                ->minDate(Carbon::today())
                ->columnSpan(6)
                ->required(),

            Select::make('tipo_permiso_id')
                ->label('Permiso de trabajo')
                ->relationship('tipoPermiso', 'descripcion')
                ->searchable()
                ->preload()
                ->reactive()
                ->columnSpan(12)
                ->afterStateUpdated(function (callable $set, $state) {
                    $permiso = TipoPermiso::find($state);

                    if (!$permiso) {
                        $set('local_id', null);
                        $set('contratistas_id', null);
                        return;
                    }

                    match ($permiso->descripcion) {
                        'Permiso tercero'   => $set('local_id', null),
                        'Permiso interno',
                        'Permiso externo'   => $set('contratistas_id', null),
                        default             => null,
                    };
                }),

            Select::make('local_id')
                ->label('Unidad Privada')
                ->relationship('local', 'descripcion')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->option_label)
                ->searchable()
                ->preload()
                ->columnSpan(12)
                ->reactive()
                ->visible(fn (callable $get) => in_array(
                    optional(TipoPermiso::find($get('tipo_permiso_id')))?->descripcion,
                    ['Permiso interno', 'Permiso externo']
                )),

            Select::make('contratistas_id')
                ->label('Contratista')
                ->relationship('contratistas', 'descripcion')
                ->searchable()
                ->preload()
                ->reactive()
                ->columnSpan(12)
                ->visible(fn (callable $get) =>
                    optional(TipoPermiso::find($get('tipo_permiso_id')))?->descripcion === 'Permiso tercero'
                ),

            CheckboxList::make('tipo_actividad')
                ->label('Tipo de actividad')
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
                ->columnSpan(12)
                ->required(),

            Textarea::make('actividad')
                ->label('Actividad a realizar')
                ->maxLength(500)
                ->rows(2)
                ->default(null),

            Textarea::make('descripcion')
                ->label('Observaciones')
                ->maxLength(500)
                ->rows(2)
                ->default(null)
                ->columnSpan(4),

            FileUpload::make('archivo_pdf')
                ->label('Subir PDF')
                ->disk('public')
                ->directory('permisos')
                ->acceptedFileTypes(['application/pdf'])
                ->getUploadedFileNameForStorageUsing(function ($file) {
                    $ultimo = \App\Models\Permiso::max('id') + 1;
                    return 'permiso_externo_' . str_pad($ultimo, 5, '0', STR_PAD_LEFT) . '.pdf';
                })
                ->columnSpan(12)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subidopor')
                ->searchable(),

            TextColumn::make('tipoPermiso.descripcion')
                ->label('Permiso trabajo')
                ->alignment('center')
                ->wrap()
                ->searchable()
                ->sortable()
                ->toggleable(),

            TextColumn::make('contratista_o_unidad')
                ->label('Contratista / Unidad Privada')
                ->alignment('center')
                ->getStateUsing(fn ($record) =>
                    $record->contratistas->descripcion
                        ?? $record->local->option_label
                        ?? '-'
                )
                ->searchable()
                ->sortable()
                ->toggleable(),

            TextColumn::make('colaborador_id')
                ->label('Autorizado')
                ->getStateUsing(fn ($record) =>
                    $record->colaborador
                        ? $record->colaborador->nombre . ' ' . $record->colaborador->apellido
                        : '-'
                )
                ->sortable()
                ->searchable(),

            TextColumn::make('rango_fechas')
                ->label('Rango de Fechas')
                ->getStateUsing(fn ($record) =>
                    Carbon::parse($record->fecha_inicio_trabajo)->format('d/m/Y') . '<br>' .
                    Carbon::parse($record->fecha_fin_trabajo)->format('d/m/Y')
                )
                ->html()
                ->color(fn ($record) =>
                    Carbon::parse($record->fecha_fin_trabajo)->isToday() ||
                    Carbon::parse($record->fecha_fin_trabajo)->isFuture()
                        ? 'success'
                        : 'danger'
                )
                ->alignment('center')
                ->sortable(query: fn ($query, $direction) => $query->orderBy('fecha_inicio_trabajo', $direction))
                ->toggleable(),

            TextColumn::make('tipo_actividad')
                ->label('Tipo de Actividad')
                ->alignment('center')
                ->formatStateUsing(fn ($state) => implode('<br>', explode(', ', $state)))
                ->html()
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('actividad')
                ->label('Actividad a realizar')
                ->alignment('center')
                ->wrap()
                ->searchable()
                ->sortable()
                ->toggleable(),

            TextColumn::make('descripcion')
                ->label('Observaciones')
                ->wrap()
                ->alignment('center')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('archivo_pdf')
                ->label('Archivo PDF')
                ->url(fn ($record) =>
                    $record->archivo_pdf
                        ? asset('storage/' . $record->archivo_pdf)
                        : null,
                    true
                )
                ->formatStateUsing(fn ($state) => $state ? basename($state) : 'Sin archivo')
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
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListPermisos::route('/'),
            'create' => Pages\CreatePermiso::route('/create'),
            'edit'   => Pages\EditPermiso::route('/{record}/edit'),
        ];
    }
}
