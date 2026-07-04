<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoriaLaboralResource\Pages;
use App\Models\Colaborador;
use App\Models\HistoriaLaboral;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HistoriaLaboralResource extends Resource
{
    protected static ?string $model = HistoriaLaboral::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Programación';
    protected static ?string $navigationLabel = 'Mis Turnos';
    protected static ?string $modelLabel       = 'Mi turno';
    protected static ?string $pluralModelLabel = 'Mis Turnos';
    protected static ?int    $navigationSort   = 5;

    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }

    /*
    |--------------------------------------------------------------------------
    | QUERY — solo los turnos del colaborador autenticado
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): Builder
    {
        $user        = Auth::user();
        $colaborador = null;

        // Buscar el colaborador vinculado al usuario autenticado
        if ($user) {
            $colaborador = Colaborador::where('user_id', $user->id)->first()
                ?? Colaborador::where('correo_corporativo', $user->email)
                              ->orWhere('correo_personal', $user->email)
                              ->first();
        }

        // Si no tiene colaborador vinculado → no mostrar nada
        if (! $colaborador) {
            return HistoriaLaboral::query()->whereRaw('0 = 1');
        }

        return HistoriaLaboral::query()
            ->with(['puestoSeguridad', 'verificaciones'])
            ->where('colaborador_id', $colaborador->id)
            ->whereHas('verificaciones', fn ($q) =>
                $q->where('estado', 'verificado')->where('tipo', 'ingreso')
            )
            ->orderByDesc('fecha')
            ->orderByDesc('hora_inicio');
    }

    /*
    |--------------------------------------------------------------------------
    | TABLA — sin columna colaborador (el guardia solo ve los suyos)
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Código — Puesto
                Tables\Columns\TextColumn::make('codigo_puesto')
                    ->label('Código — Puesto')
                    ->alignment('center')
                    ->sortable()
                    ->searchable(query: fn (Builder $q, string $search) =>
                        $q->whereHas('puestoSeguridad', fn ($q) =>
                            $q->where('codigo', 'like', "%{$search}%")
                              ->orWhere('puesto', 'like', "%{$search}%")
                        )
                    )
                    ->getStateUsing(fn ($record) =>
                        ($record->puestoSeguridad->codigo ?? '—') .
                        ' — ' .
                        ($record->puestoSeguridad->puesto ?? '—')
                    ),

                // Fecha
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->alignment('center')
                    ->sortable(),

                // Horario programado
                Tables\Columns\TextColumn::make('horario_prog')
                    ->label('Turno programado')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) =>
                        ($record->hora_inicio ? Carbon::parse($record->hora_inicio)->format('H:i') : '—') .
                        ' → ' .
                        ($record->hora_fin    ? Carbon::parse($record->hora_fin)->format('H:i')    : '—')
                    ),

                // Ingreso (hora real del escaneo QR)
                Tables\Columns\TextColumn::make('hora_ingreso')
                    ->label('Ingreso')
                    ->alignment('center')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        $v = $record->verificaciones
                            ->where('tipo', 'ingreso')
                            ->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')
                            ->first();
                        return $v?->hora_verificacion?->format('H:i') ?? '—';
                    }),

                // Salida (hora real del escaneo QR)
                Tables\Columns\TextColumn::make('hora_salida')
                    ->label('Salida')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($record) => static::tieneSalida($record) ? 'success' : 'warning')
                    ->getStateUsing(function ($record) {
                        $v = $record->verificaciones
                            ->where('tipo', 'salida')
                            ->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')
                            ->first();
                        return $v?->hora_verificacion?->format('H:i') ?? 'Pendiente';
                    }),

                // Horas trabajadas calculadas
                Tables\Columns\TextColumn::make('horas_trabajadas')
                    ->label('Horas trabajadas')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($record) => static::tieneSalida($record) ? 'success' : 'gray')
                    ->getStateUsing(function ($record) {
                        $ingreso = $record->verificaciones
                            ->where('tipo', 'ingreso')->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')->first();

                        $salida = $record->verificaciones
                            ->where('tipo', 'salida')->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')->first();

                        if (! $ingreso?->hora_verificacion || ! $salida?->hora_verificacion) {
                            return 'Sin salida';
                        }

                        $min = $ingreso->hora_verificacion->diffInMinutes($salida->hora_verificacion);
                        $h   = intdiv($min, 60);
                        $m   = str_pad($min % 60, 2, '0', STR_PAD_LEFT);

                        return "{$h}h {$m}m";
                    }),

                // Estado
                Tables\Columns\TextColumn::make('estado_turno')
                    ->label('Estado')
                    ->alignment('center')
                    ->badge()
                    ->getStateUsing(fn ($record) =>
                        static::tieneSalida($record) ? 'Completo' : 'Sin salida'
                    )
                    ->color(fn ($state) => match ($state) {
                        'Completo'   => 'success',
                        'Sin salida' => 'danger',
                        default      => 'gray',
                    }),
            ])

            ->filters([

                // Filtro por rango de fechas
                Tables\Filters\Filter::make('rango_fechas')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('desde')->label('Desde')->native(false),
                        DatePicker::make('hasta')->label('Hasta')->native(false),
                    ])
                    ->query(fn (Builder $q, array $data) =>
                        $q->when($data['desde'], fn ($q, $d) => $q->whereDate('fecha', '>=', $d))
                          ->when($data['hasta'], fn ($q, $d) => $q->whereDate('fecha', '<=', $d))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Desde: ' . Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                // Solo turnos completos
                Tables\Filters\Filter::make('solo_completos')
                    ->label('Solo turnos completos')
                    ->query(fn (Builder $q) =>
                        $q->whereHas('verificaciones', fn ($q) =>
                            $q->where('estado', 'verificado')->where('tipo', 'salida')
                        )
                    ),

            ])

            ->actions([])
            ->bulkActions([])
            ->defaultSort('fecha', 'desc')
            ->emptyStateHeading('No tienes turnos registrados')
            ->emptyStateDescription('Aquí verás tu historial de turnos una vez que hayas escaneado el ingreso.');
    }

    protected static function tieneSalida($record): bool
    {
        return $record->verificaciones
            ->where('tipo', 'salida')
            ->where('estado', 'verificado')
            ->isNotEmpty();
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistoriaLaborals::route('/'),
        ];
    }
}
