<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColaboradorResource\Pages;
use App\Filament\Resources\ColaboradorResource\Concerns\GeneraZipColaboradores;
use App\Filament\Resources\ColaboradorResource\RelationManagers\DocumentosRelationManager;
use App\Models\Colaborador;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

class ColaboradorResource extends Resource
{
    use GeneraZipColaboradores;

    protected static ?string $model = Colaborador::class;

    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?int    $navigationSort  = 3;

    public static function getModalWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([

                    // ════════════════════════════════════════
                    // PASO 1 — Información Personal
                    // ════════════════════════════════════════
                    Step::make('Personal')
                        ->icon('heroicon-o-user')
                        ->completedIcon('heroicon-m-check')
                        ->schema([

                            // Foto + datos básicos en la misma fila
                            Grid::make(12)
                                ->schema([
                                    // Foto a la izquierda
                                    FileUpload::make('foto')
                                        ->label('Cambiar foto')
                                        ->image()
                                        ->avatar()
                                        ->imageEditor()
                                        ->directory('colaboradores')
                                        ->visibility('public')
                                        ->maxSize(2048)
                                        ->columnSpan(2),

                                    // Nombre y apellido a la derecha de la foto
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('nombre')
                                                ->label('Nombre *')
                                                ->required()
                                                ->maxLength(250),

                                            TextInput::make('apellido')
                                                ->label('Apellido *')
                                                ->required()
                                                ->maxLength(250),
                                        ])
                                        ->columnSpan(10),
                                ]),

                            // Edad, fecha nacimiento, lugar nacimiento
                            Grid::make(8)
                                ->schema([
                                    TextInput::make('edad')
                                        ->label('Edad')
                                        ->suffix('años')
                                        ->readOnly()
                                        ->dehydrated(false)
                                        ->helperText('Auto')
                                        ->columnSpan(2),

                                    DatePicker::make('fecha_nacimiento')
                                        ->label('Fecha de nacimiento *')
                                        ->native(false)
                                        ->maxDate(now()->subYears(16))
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, callable $set) =>
                                            $set('edad', $state ? Carbon::parse($state)->age : null)
                                        )
                                        ->columnSpan(3),

                                    TextInput::make('lugarnacimiento')
                                        ->label('Lugar de nacimiento *')
                                        ->maxLength(250)
                                        ->columnSpan(3),
                                ]),

                            // Género y estado civil
                            Grid::make(4)
                                ->schema([
                                    Select::make('genero_id')
                                        ->label('Género *')
                                        ->relationship('genero', 'descripcion')
                                        ->searchable()->required()->preload()
                                        ->columnSpan(2),

                                    Select::make('estado_civil_id')
                                        ->label('Estado civil *')
                                        ->relationship('estadoCivil', 'descripcion')
                                        ->searchable()->required()->preload()
                                        ->columnSpan(2),
                                ]),

                            // Documento
                            Section::make('Documento')
                                ->schema([
                                    Grid::make(6)
                                        ->schema([
                                            Select::make('tipo_documento_id')
                                                ->label('Tipo de documento *')
                                                ->relationship('tipoDocumento', 'descripcion')
                                                ->searchable()->required()->preload()
                                                ->columnSpan(3),

                                            TextInput::make('documento')
                                                ->label('Número *')
                                                ->required()
                                                ->maxLength(20)
                                                ->unique(
                                                    table: 'colaboradors',
                                                    column: 'documento',
                                                    ignoreRecord: true
                                                )
                                                ->validationMessages([
                                                    'unique' => 'Ya existe un colaborador con este número.',
                                                ])
                                                ->columnSpan(1),

                                            Select::make('grupo_sanguineo_id')
                                                ->label('Grupo sanguíneo')
                                                ->relationship('grupoSanguineo', 'descripcion')
                                                ->searchable()->preload()
                                                ->columnSpan(2),
                                        ]),
                                ])
                                ->compact(),
                        ]),

                    // ════════════════════════════════════════
                    // PASO 2 — Información de Contacto
                    // ════════════════════════════════════════
                    Step::make('Contacto')
                        ->icon('heroicon-o-phone')
                        ->completedIcon('heroicon-m-check')
                        ->schema([

                            Section::make('Información de contacto')
                                ->schema([
                                    Grid::make(6)
                                        ->schema([
                                            TextInput::make('barrio')
                                                ->label('Barrio *')
                                                ->maxLength(250)
                                                ->columnSpan(2),

                                            TextInput::make('direccion')
                                                ->label('Dirección *')
                                                ->maxLength(250)
                                                ->columnSpan(4),
                                        ]),

                                    Grid::make(4)
                                        ->schema([
                                            TextInput::make('celular')
                                                ->label('Celular *')
                                                ->required()
                                                ->tel()
                                                ->maxLength(20)
                                                ->columnSpan(2),

                                            TextInput::make('telefono')
                                                ->label('Teléfono')
                                                ->tel()
                                                ->maxLength(20)
                                                ->nullable()
                                                ->columnSpan(2),
                                        ]),

                                    TextInput::make('correo_personal')
                                        ->label('Correo electrónico *')
                                        ->email()
                                        ->maxLength(250)
                                        ->columnSpanFull(),
                                ])
                                ->compact(),
                        ]),

                    // ════════════════════════════════════════
                    // PASO 3 — Información Laboral
                    // ════════════════════════════════════════
                    Step::make('Laboral')
                        ->icon('heroicon-o-briefcase')
                        ->completedIcon('heroicon-m-check')
                        ->schema([

                            Section::make('Información laboral')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Select::make('departamento_id')
                                                ->label('Departamento *')
                                                ->relationship('departamento', 'descripcion')
                                                ->searchable()->required()->preload()
                                                ->live()
                                                ->afterStateUpdated(fn (callable $set) =>
                                                    $set('area_id', null)
                                                ),

                                            Select::make('area_id')
                                                ->label('Área *')
                                                ->relationship(
                                                    'area', 'descripcion',
                                                    modifyQueryUsing: fn (Forms\Get $get, $query) =>
                                                        $get('departamento_id')
                                                            ? $query->where('departamento_id', $get('departamento_id'))
                                                            : $query
                                                )
                                                ->searchable()->required()->preload()
                                                ->live()
                                                ->afterStateUpdated(fn (callable $set) =>
                                                    $set('cargo_id', null)
                                                ),
                                        ]),

                                    Select::make('cargo_id')
                                        ->label('Cargo *')
                                        ->relationship(
                                            'cargo', 'descripcion',
                                            modifyQueryUsing: fn (Forms\Get $get, $query) =>
                                                $get('area_id')
                                                    ? $query->where('area_id', $get('area_id'))
                                                    : $query
                                        )
                                        ->searchable()->required()->preload()
                                        ->columnSpanFull(),
                                ])
                                ->compact(),

                            Section::make('Contrato')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            Select::make('tipo_contrato_id')
                                                ->label('Tipo de contrato *')
                                                ->relationship('tipoContrato', 'descripcion')
                                                ->searchable()->required()->preload(),

                                            DatePicker::make('fechainiciolab')
                                                ->label('Fecha de inicio *')
                                                ->native(false)
                                                ->default(now()),

                                            DatePicker::make('fechafinlab')
                                                ->label('Fecha de terminación')
                                                ->native(false)
                                                ->nullable()
                                                ->helperText('Vacío = activo'),
                                        ]),
                                ])
                                ->compact(),

                            Section::make('Acceso al sistema')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Placeholder::make('estado_usuario')
                                                ->label('Usuario')
                                                ->content(fn (?Colaborador $record) =>
                                                    $record?->user_id && $record->user
                                                        ? "✅ {$record->user->name} ({$record->user->email})"
                                                        : '— Sin usuario asignado'
                                                ),

                                            TextInput::make('correo_corporativo')
                                                ->label('Correo corporativo')
                                                ->email()
                                                ->maxLength(250),
                                        ]),
                                ])
                                ->compact(),
                        ]),

                ])
                ->columnSpanFull()
                ->skippable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/images/avatar-default.png')),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Colaborador')
                    ->sortable()->searchable(['nombre', 'apellido'])
                    ->formatStateUsing(fn ($record) =>
                        $record->nombre . ' ' . $record->apellido
                    ),

                Tables\Columns\TextColumn::make('documento')
                    ->label('N° Documento')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->searchable()->alignment('center')
                    ->copyable()->copyMessage('Copiado'),

                Tables\Columns\TextColumn::make('cargo.descripcion')
                    ->label('Cargo')
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\IconColumn::make('tiene_usuario')
                    ->label('Acceso')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) => $record->user_id !== null)
                    ->boolean()
                    ->trueIcon('heroicon-m-key')->falseIcon('heroicon-m-minus')
                    ->trueColor('success')->falseColor('gray')
                    ->tooltip(fn ($record) =>
                        $record->user_id ? "Usuario: {$record->user?->email}" : 'Sin acceso'
                    ),

                Tables\Columns\TextColumn::make('estado_laboral')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->fechafinlab ? 'Inactivo' : 'Activo')
                    ->color(fn ($state) => $state === 'Activo' ? 'success' : 'danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'descripcion')
                    ->searchable()->preload(),

                Tables\Filters\TernaryFilter::make('estado_laboral')
                    ->label('Estado laboral')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')->falseLabel('Inactivos')
                    ->queries(
                        true:  fn ($q) => $q->whereNull('fechafinlab'),
                        false: fn ($q) => $q->whereNotNull('fechafinlab'),
                    ),

                Tables\Filters\TernaryFilter::make('tiene_usuario')
                    ->label('Acceso al sistema')
                    ->placeholder('Todos')
                    ->trueLabel('Con usuario')->falseLabel('Sin usuario')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('user_id'),
                        false: fn ($q) => $q->whereNull('user_id'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->modalWidth(MaxWidth::FiveExtraLarge),

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
                    Tables\Actions\BulkAction::make('descargarDocumentosGrupal')
                        ->label('Descargar documentos (ZIP)')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('info')
                        ->action(fn ($records) => static::descargarZipGrupal($records))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('nombre', 'asc');
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
            //'edit'  => Pages\EditColaborador::route('/'),
        ];
    }
}
