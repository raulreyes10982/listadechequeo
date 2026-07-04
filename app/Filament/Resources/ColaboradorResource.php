<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColaboradorResource\Pages;
use App\Models\Colaborador;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\ColaboradorResource\Concerns\GeneraZipColaboradores;
use App\Filament\Resources\ColaboradorResource\RelationManagers\DocumentosRelationManager;
use Filament\Forms\Components\{TextInput, Select, DatePicker, Wizard, Wizard\Step, FileUpload};

class ColaboradorResource extends Resource
{
    use GeneraZipColaboradores;

    protected static ?string $model = Colaborador::class;

    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?int    $navigationSort  = 1;

    public static function getModalWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    Step::make('Información Personal')
                        ->schema([
                            // ✅ Foto del colaborador
                            FileUpload::make('foto')
                                ->label('Fotografía')
                                ->image()
                                ->avatar() // recorte circular en el uploader
                                ->imageEditor()
                                ->directory('colaboradores')
                                ->visibility('public')
                                ->maxSize(2048) // 2MB
                                ->columnSpan(2),

                            TextInput::make('nombre')
                                ->label('Nombre')
                                ->columnSpan(3)
                                ->maxLength(250)
                                ->required(),

                            TextInput::make('apellido')
                                ->label('Apellido')
                                ->columnSpan(3)
                                ->maxLength(250)
                                ->required(),

                            TextInput::make('edad')
                                ->label('Edad')
                                ->columnSpan(2)
                                ->readOnly()
                                ->suffix('años')
                                ->dehydrated(false)
                                ->helperText('Se calcula automáticamente'),

                            DatePicker::make('fecha_nacimiento')
                                ->label('Fecha de Nacimiento')
                                ->columnSpan(2)
                                ->native(false)
                                ->firstDayOfWeek(7)
                                ->reactive()
                                ->maxDate(now()->subYears(16))
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('edad', $state ? Carbon::parse($state)->age : null);
                                }),

                            TextInput::make('lugarnacimiento')
                                ->label('Lugar de Nacimiento')
                                ->columnSpan(4)
                                ->maxLength(250),

                            Select::make('genero_id')
                                ->label('Género')
                                ->columnSpan(2)
                                ->relationship('genero', 'descripcion')
                                ->searchable()->required()->preload(),

                            Select::make('tipo_documento_id')
                                ->label('Tipo de Documento')
                                ->columnSpan(2)
                                ->relationship('tipoDocumento', 'descripcion')
                                ->searchable()->required()->preload(),

                            TextInput::make('documento')
                                ->label('Número de Identidad')
                                ->columnSpan(2)
                                ->maxLength(20)
                                ->required()
                                ->unique(
                                    table: 'colaboradors',
                                    column: 'documento',
                                    ignoreRecord: true
                                )
                                ->validationMessages([
                                    'unique' => 'Ya existe un colaborador registrado con este número de documento.',
                                ]),

                            Select::make('estado_civil_id')
                                ->label('Estado Civil')
                                ->columnSpan(2)
                                ->relationship('estadoCivil', 'descripcion')
                                ->searchable()->required()->preload(),

                            Select::make('grupo_sanguineo_id')
                                ->label('Grupo Sanguíneo')
                                ->columnSpan(2)
                                ->relationship('grupoSanguineo', 'descripcion')
                                ->searchable()->required()->preload(),
                        ]),

                    Step::make('Información de Contacto')
                        ->schema([
                            TextInput::make('barrio')
                                ->label('Barrio')
                                ->columnSpan(4)->maxLength(250),

                            TextInput::make('direccion')
                                ->label('Dirección')
                                ->columnSpan(4)->maxLength(250),

                            TextInput::make('celular')
                                ->label('Celular')
                                ->columnSpan(2)
                                ->maxLength(20)
                                ->required()
                                ->tel(),

                            TextInput::make('telefono')
                                ->label('Teléfono')
                                ->columnSpan(2)->tel()->maxLength(20),

                            TextInput::make('correo_personal')
                                ->label('Email personal')
                                ->columnSpan(4)->email()->maxLength(250),
                        ]),

                    Step::make('Información Laboral')
                        ->schema([
                            Select::make('departamento_id')
                                ->label('Departamento')
                                ->columnSpan(2)
                                ->relationship('departamento', 'descripcion')
                                ->searchable()->required()->preload()
                                ->live() // ✅ para refrescar áreas según departamento
                                ->afterStateUpdated(fn (callable $set) => $set('area_id', null)),

                            Select::make('area_id')
                                ->label('Área')
                                ->columnSpan(3)
                                ->relationship(
                                    'area',
                                    'descripcion',
                                    // ✅ Solo muestra áreas del departamento seleccionado
                                    modifyQueryUsing: fn (Forms\Get $get, $query) =>
                                        $get('departamento_id')
                                            ? $query->where('departamento_id', $get('departamento_id'))
                                            : $query
                                )
                                ->searchable()->required()->preload()
                                ->live()
                                ->afterStateUpdated(fn (callable $set) => $set('cargo_id', null)),

                            Select::make('cargo_id')
                                ->label('Cargo')
                                ->columnSpan(3)
                                ->relationship(
                                    'cargo',
                                    'descripcion',
                                    // ✅ Solo muestra cargos del área seleccionada
                                    modifyQueryUsing: fn (Forms\Get $get, $query) =>
                                        $get('area_id')
                                            ? $query->where('area_id', $get('area_id'))
                                            : $query
                                )
                                ->searchable()->required()->preload(),

                            Select::make('tipo_contrato_id')
                                ->label('Tipo de Contrato')
                                ->columnSpan(4)
                                ->relationship('tipoContrato', 'descripcion')
                                ->searchable()->required()->preload(),

                            DatePicker::make('fechainiciolab')
                                ->label('Fecha inicio laboral')
                                ->columnSpan(2)
                                ->native(false)
                                ->default(now()),

                            DatePicker::make('fechafinlab')
                                ->label('Fecha de Terminación')
                                ->columnSpan(2)
                                ->native(false)
                                ->afterOrEqual('fechainiciolab') // ✅ no permite fecha fin antes de inicio
                                ->helperText('Dejar vacío si el colaborador está activo'),

                            // ℹ️ Informativo: el user_id real se asigna desde UserResource
                            Forms\Components\Placeholder::make('usuario_info')
                                ->label('Usuario del sistema')
                                ->columnSpan(4)
                                ->content(fn (?Colaborador $record) =>
                                    $record?->user
                                        ? "✅ Vinculado: {$record->user->email}"
                                        : '— Sin usuario asignado. Para crear acceso, ve a Usuarios → Crear.'
                                ),

                            TextInput::make('correo_corporativo')
                                ->label('Email corporativo')
                                ->columnSpan(4)->email()->maxLength(250),
                        ]),
                ])
                ->columns(8)
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ Foto del colaborador como avatar circular
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/avatar-default.png'))
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()->searchable()->alignment('center')
                    ->formatStateUsing(fn ($record) => $record->nombre . ' ' . $record->apellido)
                    ->searchable(['nombre', 'apellido']),

                Tables\Columns\TextColumn::make('documento')
                    ->label('N° Documento')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('tipoDocumento.descripcion')
                    ->label('Tipo Doc.')
                    ->sortable()->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->searchable()->alignment('center')
                    ->copyable()
                    ->copyMessage('Celular copiado'),

                Tables\Columns\TextColumn::make('edad')
                    ->label('Edad')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) =>
                        $record->fecha_nacimiento
                            ? Carbon::parse($record->fecha_nacimiento)->age . ' años'
                            : '—'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('departamento.descripcion')
                    ->label('Departamento')
                    ->sortable()->searchable()->alignment('center')
                    ->badge()->color('info'),

                Tables\Columns\TextColumn::make('area.descripcion')
                    ->label('Área')
                    ->sortable()->searchable()->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cargo.descripcion')
                    ->label('Cargo')
                    ->sortable()->searchable()->alignment('center'),

                // ✅ Indicador visual de acceso al sistema
                Tables\Columns\IconColumn::make('tiene_usuario')
                    ->label('Acceso')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) => $record->user_id !== null)
                    ->boolean()
                    ->trueIcon('heroicon-m-key')
                    ->falseIcon('heroicon-m-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) => $record->user_id ? "Usuario: {$record->user?->email}" : 'Sin acceso al sistema'),

                Tables\Columns\TextColumn::make('correo_corporativo')
                    ->label('Email Corporativo')
                    ->limit(20)->sortable()->searchable()->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tipoContrato.descripcion')
                    ->label('Tipo Contrato')
                    ->sortable()->searchable()->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fechainiciolab')
                    ->label('Inicio')
                    ->date('d/m/Y')->sortable()->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fechafinlab')
                    ->label('Fin')
                    ->date('d/m/Y')->sortable()->alignment('center')
                    ->placeholder('Activo')
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ✅ Filtros por departamento, área y género
                Tables\Filters\SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'descripcion')
                    ->searchable()->preload(),

                Tables\Filters\SelectFilter::make('area_id')
                    ->label('Área')
                    ->relationship('area', 'descripcion')
                    ->searchable()->preload(),

                Tables\Filters\SelectFilter::make('genero_id')
                    ->label('Género')
                    ->relationship('genero', 'descripcion')
                    ->preload(),

                // ✅ Filtro de estado laboral
                Tables\Filters\TernaryFilter::make('estado_laboral')
                    ->label('Estado laboral')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->queries(
                        true:  fn ($query) => $query->whereNull('fechafinlab'),
                        false: fn ($query) => $query->whereNotNull('fechafinlab'),
                    ),

                // ✅ Filtro de acceso al sistema
                Tables\Filters\TernaryFilter::make('tiene_usuario')
                    ->label('Acceso al sistema')
                    ->placeholder('Todos')
                    ->trueLabel('Con usuario')
                    ->falseLabel('Sin usuario')
                    ->queries(
                        true:  fn ($query) => $query->whereNotNull('user_id'),
                        false: fn ($query) => $query->whereNull('user_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(static::getModalWidth()),

                // ✅ Descarga individual de documentos en ZIP
                Tables\Actions\Action::make('descargarDocumentos')
                    ->label('Documentos')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('info')
                    ->action(fn (Colaborador $record) => static::descargarZipIndividual($record))
                    ->visible(fn (Colaborador $record) => $record->documentos()->exists()),

                Tables\Actions\DeleteAction::make()->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    // ✅ Descarga grupal — ZIP con carpeta por colaborador
                    Tables\Actions\BulkAction::make('descargarDocumentosGrupal')
                        ->label('Descargar documentos (ZIP)')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('info')
                        ->action(fn ($records) => static::descargarZipGrupal($records))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('nombre', 'asc'); // ✅ orden alfabético por defecto
    }

    public static function getRelations(): array
    {
        return [
            DocumentosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColaboradors::route('/'),
        ];
    }
}
