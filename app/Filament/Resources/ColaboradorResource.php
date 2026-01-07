<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ColaboradorResource\Pages;
use App\Filament\Resources\ColaboradorResource\RelationManagers;
use App\Models\Colaborador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\{TextInput, Select, DatePicker, Wizard, Wizard\Step};


class ColaboradorResource extends Resource
{
    protected static ?string $model = Colaborador::class;

    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationLabel = 'Colaboradores';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Información Personal')
                        ->schema([
                            TextInput::make('nombre')->label('Nombre')->columnSpan(4)->maxLength(250),
                            TextInput::make('apellido')->label('Apellido')->columnSpan(3)->maxLength(250),
                            TextInput::make('edad')->label('Edad')->columnSpan(1)->default(fn ($record) => $record?->edad),
                            DatePicker::make('fecha_nacimiento')->label('Fecha de Nacimiento')->columnSpan(2)->native(false)->firstDayOfWeek(7)->reactive()->afterStateUpdated(function ($state, callable $set) {if ($state) {$edad = \Carbon\Carbon::parse($state)->age; $set('edad', $edad);}}),
                            TextInput::make('lugarnacimiento')->label('Lugar de Nacimiento')->columnSpan(4)->maxLength(250),
                            Select::make('genero_id')->label('Género')->columnSpan(2)->relationship('genero', 'descripcion')->searchable()->required()->preload(),
                            Select::make('tipo_documento_id')->label('Tipo de Documento') ->columnSpan(2)->relationship('tipoDocumento', 'descripcion')->searchable()->required()->preload(),
                            TextInput::make('documento')->label('Número de Identidad')->columnSpan(2)->numeric(),
                            Select::make('estado_civil_id')->label('Estado Civil')->columnSpan(2)->relationship('estadoCivil', 'descripcion')->searchable()->required()->preload(),
                            Select::make('grupo_sanguineo_id')->label('Grupo Sanguíneo')->required()->relationship('grupoSanguineo', 'descripcion')->searchable()->preload()->columnSpan(2),
                        ]),

                    Step::make('Información de Contacto')
                            ->schema([
                            TextInput::make('barrio')->label('Barrio')->columnSpan(4)->maxLength(250),
                            TextInput::make('direccion')->label('Dirección')->columnSpan(4)->maxLength(250),
                            TextInput::make('celular')->label('Celular')->columnSpan(2)->numeric(),
                            TextInput::make('telefono')->label('Teléfono')->columnSpan(2)->tel()->numeric(),
                            TextInput::make('correo_personal')->label('Email personal')->columnSpan(4)->email()->maxLength(250),
                        ]),

                    Step::make('Información Laboral')
                        ->schema([
                            Select::make('departamento_id')->label('Departamento')->columnSpan(2)->relationship('departamento', 'descripcion')->searchable()->required()->preload(),
                            Select::make('area_id')->label('Área')->columnSpan(2)->relationship('area', 'descripcion')->searchable()->required()->preload(),
                            Select::make('cargo_id')->label('Cargo')->columnSpan(2)->relationship('cargo', 'descripcion')->searchable()->required()->preload(),
                            TextInput::make('correo_corporativo')->label('Email corporativo')->columnSpan(2)->email()->maxLength(250),
                            Select::make('tipo_contrato_id')->label('Tipo de Contrato')->columnSpan(2)->relationship('tipoContrato', 'descripcion')->searchable()->required() ->preload(),
                            Select::make('user_id')->label('Usuario del sistema')->columnSpan(2)->relationship('user', 'email')->searchable()->nullable()->placeholder('Sin usuario asignado'),
                            DatePicker::make('fechainiciolab')->columnSpan(2),
                            DatePicker::make('fechafinlab')->label('Fecha de Terminación')->columnSpan(2),
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
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->sortable()->searchable()->alignment('center')->formatStateUsing(fn($record) => $record->nombre . ' ' . $record->apellido),
                Tables\Columns\TextColumn::make('fecha_nacimiento')->label('Fecha de nacimiento')->sortable()->searchable()->date('d-M-Y')->alignment('center'),
                Tables\Columns\TextColumn::make('edad')->label('Edad')->sortable()->searchable()->alignment('center'),
                Tables\Columns\TextColumn::make('tipoDocumento.descripcion')->label('Tipo Documento')->numeric()->sortable()->searchable()->alignment('center'),
                Tables\Columns\TextColumn::make('documento')->label('N° Documento')->sortable()->searchable()->alignment('center'),
                Tables\Columns\TextColumn::make('genero.descripcion')->label('Género')->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('grupoSanguineo.descripcion')->label('Grupo Sanguíneo')->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('departamento.descripcion')->label('Departamento')->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('area.descripcion')->label('Área')->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cargo.descripcion')->label('Cargo')->sortable()->searchable()->alignment('center'),
                Tables\Columns\TextColumn::make('correo_corporativo')->label('Email Corporativo')->toggleable()->limit(20)->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipoContrato.descripcion')->label('Tipo Contrato')->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fechainiciolab')->label('Inicio')->date()->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fechafinlab')->label('Fin')->date()->sortable()->searchable()->alignment('center')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('editar')->modalWidth(MaxWidth::FiveExtraLarge),
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
            'index' => Pages\ListColaboradors::route('/'),
            //'create' => Pages\CreateColaborador::route('/create'),
            //'edit' => Pages\EditColaborador::route('/{record}/edit'),
        ];
    }
}
