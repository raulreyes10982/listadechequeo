<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificacionTurnoResource\Pages;
use App\Models\VerificacionTurno;
use App\Models\Colaborador;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerificacionTurnoResource extends Resource
{
    protected static ?string $model = VerificacionTurno::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Verificación de Turnos';
    protected static ?int $navigationSort = 3; 

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('registrar_turno_id')
                ->relationship('turno', 'id')
                ->label('Turno Programado')
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('tipo')
                ->options([
                    'ingreso' => 'Ingreso',
                    'ronda' => 'Ronda',
                    'salida' => 'Salida',
                    'reemplazo' => 'Reemplazo',
                ])
                ->label('Tipo de Verificación')
                ->required(),

            Forms\Components\DateTimePicker::make('hora_verificacion')
                ->label('Hora de Verificación')
                ->default(now())
                ->disabled(),

            Forms\Components\Textarea::make('observacion')
                ->label('Observación')
                ->rows(3),

            Forms\Components\Hidden::make('verificado_por')
                ->default(fn () => Auth::id())
                ->dehydrated(),

            Forms\Components\Select::make('estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'verificado' => 'Verificado',
                    'cerrado' => 'Cerrado',
                ])
                ->label('Estado')
                ->default('pendiente'),
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
                Tables\Columns\TextColumn::make('turno.colaborador.nombre')
                    ->label('Colaborador')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->turno && $record->turno->colaborador) {
                            return $record->turno->colaborador->nombre . ' ' . $record->turno->colaborador->apellido;
                        }
                        return $state ?? 'N/A';
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('turno.puestoSeguridad.puesto')
                    ->label('Puesto')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->turno && $record->turno->puestoSeguridad) {
                            $codigo = $record->turno->puestoSeguridad->codigo ?? '';
                            $puesto = $record->turno->puestoSeguridad->puesto ?? '';
                            
                            if (!empty($codigo) && !empty($puesto)) {
                                return $codigo . ' - ' . $puesto;
                            } elseif (!empty($puesto)) {
                                return $puesto;
                            }
                        }
                        return $state ?? 'N/A';
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn ($record) => match ($record->tipo) {
                        'ingreso' => 'info',
                        'ronda' => 'warning',
                        'salida' => 'success',
                        'reemplazo' => 'secondary',
                    })
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('hora_verificacion')
                    ->dateTime('d/m/Y H:i')
                    ->label('Hora'),

                Tables\Columns\TextColumn::make('verificador.name')
                    ->label('Verificado por'),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn ($record) => match ($record->estado) {
                        'pendiente' => 'warning',
                        'verificado' => 'success',
                        'cerrado' => 'danger',
                    })
                    ->label('Estado'),
            ])

            ->actions([
                Action::make('scanQr')
                    ->label('Escanear QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->modalHeading('Escanear Código QR del Puesto')
                    ->modalWidth('lg')
                    ->modalContent(fn ($record) => view('filament.qr-scanner', [
                        'verificacionId' => $record->id,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->visible(function ($record): bool {
                        // Obtener usuario actual
                        $user = Auth::user();
                        
                        if (!$user) {
                            return false;
                        }
                        
                        // 1. La verificación debe estar pendiente
                        if ($record->estado !== 'pendiente') {
                            return false;
                        }
                        
                        // 2. Determinar si el usuario es administrador
                        $esAdmin = false;
                        $userModel = User::with('roles')->find($user->id);
                        
                        if (method_exists($userModel, 'hasRole')) {
                            $esAdmin = $userModel->hasRole(['admin', 'super_admin']);
                        } elseif ($userModel->roles) {
                            $esAdmin = $userModel->roles->whereIn('name', ['admin', 'super_admin'])->isNotEmpty();
                        } elseif (isset($userModel->role)) {
                            $esAdmin = in_array($userModel->role, ['admin', 'super_admin']);
                        }
                        
                        Log::info('Visibilidad Escanear QR:', [
                            'verificacion_id' => $record->id,
                            'user_id' => $user->id,
                            'es_admin' => $esAdmin,
                            'estado' => $record->estado
                        ]);
                        
                        // 3. Si es administrador, NO mostrar el botón
                        if ($esAdmin) {
                            Log::info('❌ Admin no ve botón Escanear QR');
                            return false;
                        }
                        
                        // 4. Buscar colaborador asociado al usuario
                        $colaborador = Colaborador::where('user_id', $user->id)->first();
                        
                        if (!$colaborador) {
                            $colaborador = Colaborador::where('correo_corporativo', $user->email)
                                ->orWhere('correo_personal', $user->email)
                                ->first();
                        }
                        
                        // 5. Verificar si esta verificación pertenece al colaborador del usuario
                        $perteneceAlUsuario = false;
                        if ($colaborador && $record->turno) {
                            $perteneceAlUsuario = $record->turno->colaborador_id == $colaborador->id;
                        }
                        
                        Log::info('Resultado visibilidad:', [
                            'tiene_colaborador' => $colaborador ? 'Sí' : 'No',
                            'colaborador_id' => $colaborador ? $colaborador->id : null,
                            'turno_colaborador_id' => $record->turno ? $record->turno->colaborador_id : null,
                            'pertenece_al_usuario' => $perteneceAlUsuario,
                            'mostrar_boton' => $perteneceAlUsuario ? 'Sí' : 'No'
                        ]);
                        
                        // 6. Mostrar solo si pertenece al usuario
                        return $perteneceAlUsuario;
                    }),
            ])
            
            ->bulkActions([])
            ->emptyStateActions([]);
    }

    /*
    |--------------------------------------------------------------------------
    | PÁGINAS FILAMENT
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerificacionTurnos::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    /*
    |--------------------------------------------------------------------------
    | FILTRAR SEGÚN ROL DE USUARIO - VERSIÓN SIMPLIFICADA
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();
        
        // Si no hay usuario autenticado, no mostrar nada
        if (!$user) {
            return $query->where('id', 0);
        }
        
        // PRIMERO: Verificar si el usuario es administrador
        $userWithRoles = User::with('roles')->find($user->id);
        
        $esAdmin = false;
        
        // Método 1: Verificar con hasRole (si existe el método)
        if (method_exists($userWithRoles, 'hasRole')) {
            $esAdmin = $userWithRoles->hasRole(['admin', 'super_admin']);
        }
        // Método 2: Verificar con relación roles
        elseif ($userWithRoles->roles) {
            $esAdmin = $userWithRoles->roles->whereIn('name', ['admin', 'super_admin'])->isNotEmpty();
        }
        // Método 3: Verificar columna 'role' directamente
        elseif (isset($userWithRoles->role)) {
            $esAdmin = in_array($userWithRoles->role, ['admin', 'super_admin']);
        }
        
        Log::info('Filtro de verificaciones:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'es_admin' => $esAdmin,
            'roles' => $userWithRoles->roles ? $userWithRoles->roles->pluck('name')->toArray() : []
        ]);
        
        // SI ES ADMIN: mostrar todos los registros
        if ($esAdmin) {
            return $query;
        }
        
        // NO ES ADMIN: buscar colaborador asociado
        $colaborador = Colaborador::where('user_id', $user->id)->first();
        
        if (!$colaborador) {
            $colaborador = Colaborador::where('correo_corporativo', $user->email)
                ->orWhere('correo_personal', $user->email)
                ->first();
        }
        
        // Si encontró colaborador, filtrar por él
        if ($colaborador) {
            return $query->whereHas('turno', function ($q) use ($colaborador) {
                $q->where('colaborador_id', $colaborador->id);
            });
        }
        
        // Si no es admin y no tiene colaborador, no mostrar nada
        return $query->where('id', 0);
    }
}