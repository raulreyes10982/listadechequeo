<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Colaborador;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?int    $navigationSort  = 2;

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Vincular colaborador')
                    ->description('Selecciona el colaborador para autocompletar nombre y correo')
                    ->schema([
                        // ✅ Select que muestra nombre+apellido del colaborador
                        // Al seleccionar, auto-llena name y email
                        Select::make('colaborador_id')
                            ->label('Colaborador')
                            ->options(
                                Colaborador::all()->mapWithKeys(fn ($c) => [
                                    $c->id => trim("{$c->nombre} {$c->apellido}") .
                                            ($c->user_id ? ' (ya tiene usuario)' : ''),
                                ])
                            )
                            ->searchable()
                            ->placeholder('Buscar colaborador...')
                            ->nullable()
                            ->live()
                            ->dehydrated(false) // No se guarda en users, se guarda en colaboradors
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) return;

                                $colaborador = Colaborador::find($state);
                                if (! $colaborador) return;

                                // ✅ Auto-completar nombre y email desde el colaborador
                                $set('name', trim("{$colaborador->nombre} {$colaborador->apellido}"));

                                if ($colaborador->correo_corporativo) {
                                    $set('email', $colaborador->correo_corporativo);
                                } elseif ($colaborador->correo_personal) {
                                    $set('email', $colaborador->correo_personal);
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Datos de acceso')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            // ✅ Requerida solo al CREAR, no al editar
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText(fn (string $operation) =>
                                $operation === 'edit'
                                    ? 'Deja vacío para mantener la contraseña actual'
                                    : 'Mínimo 8 caracteres'
                            )
                            ->columnSpan(2),

                        Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Estado de la cuenta')
                    ->schema([
                        // ✅ Toggle activo/inactivo
                        Toggle::make('is_active')
                            ->label('Usuario activo')
                            ->helperText('Desactívalo para bloquear el acceso sin eliminar el usuario')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1),

                        // ✅ Contraseña temporal
                        Toggle::make('must_change_password')
                            ->label('Contraseña temporal')
                            ->helperText('El usuario deberá cambiar su contraseña al primer ingreso')
                            ->default(false)
                            ->onColor('warning')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLA
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color('info')
                    ->separator(','),

                Tables\Columns\TextColumn::make('colaborador.cargo.descripcion')
                    ->label('Cargo')
                    ->placeholder('Sin cargo')
                    ->toggleable(isToggledHiddenByDefault: true),

                // ✅ Badge de estado activo/inactivo
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->alignment('center'),

                // ✅ Badge de contraseña temporal
                Tables\Columns\IconColumn::make('must_change_password')
                    ->label('Pwd temporal')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->trueIcon('heroicon-m-key')
                    ->falseIcon('heroicon-m-minus')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->placeholder('Todos'),

                Tables\Filters\TernaryFilter::make('must_change_password')
                    ->label('Contraseña temporal')
                    ->trueLabel('Pendiente cambio')
                    ->falseLabel('Contraseña normal')
                    ->placeholder('Todos'),
            ])
            ->actions([
                // ✅ Acción rápida de activar/desactivar desde la tabla
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open')
                    ->color(fn ($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'Desactivar usuario' : 'Activar usuario')
                    ->modalDescription(fn ($record) =>
                        $record->is_active
                            ? "El usuario {$record->name} quedará bloqueado y no podrá iniciar sesión."
                            : "El usuario {$record->name} podrá iniciar sesión nuevamente."
                    )
                    ->action(fn ($record) =>
                        $record->is_active ? $record->deactivate() : $record->activate()
                    ),

                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()->label('Eliminar'),
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
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
