<?php

namespace App\Filament\Widgets;

use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class GuardiasHoyWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '👮 Guardias programados hoy';

    public function table(Table $table): Table
    {
        $hoy = Carbon::today();
        $now = Carbon::now();

        return $table
            ->query(
                RegistrarTurno::query()
                    ->with(['colaborador', 'puestoSeguridad', 'verificaciones'])
                    ->whereDate('fecha', $hoy)
                    ->orderBy('hora_inicio')
            )
            ->columns([

                Tables\Columns\TextColumn::make('colaborador')
                    ->label('Guardia')
                    ->getStateUsing(fn ($record) =>
                        trim(($record->colaborador->nombre ?? '') . ' ' . ($record->colaborador->apellido ?? ''))
                    )
                    ->searchable(query: fn ($q, $s) =>
                        $q->whereHas('colaborador', fn ($q) =>
                            $q->where('nombre', 'like', "%{$s}%")->orWhere('apellido', 'like', "%{$s}%")
                        )
                    ),

                Tables\Columns\TextColumn::make('puesto')
                    ->label('Puesto')
                    ->getStateUsing(fn ($record) =>
                        ($record->puestoSeguridad->codigo ?? '—') .
                        ' — ' .
                        ($record->puestoSeguridad->puesto ?? '—')
                    ),

                Tables\Columns\TextColumn::make('horario')
                    ->label('Horario programado')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) =>
                        Carbon::parse($record->hora_inicio)->format('H:i') .
                        ' → ' .
                        Carbon::parse($record->hora_fin)->format('H:i')
                    ),

                Tables\Columns\TextColumn::make('hora_ingreso')
                    ->label('Ingreso')
                    ->alignment('center')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        $v = $record->verificaciones
                            ->where('tipo', 'ingreso')->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')->first();
                        return $v?->hora_verificacion?->format('H:i') ?? '—';
                    }),

                Tables\Columns\TextColumn::make('hora_salida')
                    ->label('Salida')
                    ->alignment('center')
                    ->badge()
                    ->color(fn ($record) =>
                        $record->verificaciones->where('tipo','salida')->where('estado','verificado')->isNotEmpty()
                            ? 'success' : 'gray'
                    )
                    ->getStateUsing(function ($record) {
                        $v = $record->verificaciones
                            ->where('tipo', 'salida')->where('estado', 'verificado')
                            ->sortBy('hora_verificacion')->first();
                        return $v?->hora_verificacion?->format('H:i') ?? '—';
                    }),

                Tables\Columns\TextColumn::make('estado_guardia')
                    ->label('Estado')
                    ->alignment('center')
                    ->badge()
                    ->getStateUsing(function ($record) use ($now) {
                        $verificaciones = $record->verificaciones;
                        $tieneIngreso = $verificaciones->where('tipo','ingreso')->where('estado','verificado')->isNotEmpty();
                        $tieneSalida  = $verificaciones->where('tipo','salida')->where('estado','verificado')->isNotEmpty();
                        $horaInicio   = Carbon::parse($record->hora_inicio);
                        $horaFin      = Carbon::parse($record->hora_fin);

                        $tieneVencido  = $verificaciones->where('tipo','salida')->where('estado','vencido')->isNotEmpty();

                        if ($tieneSalida)  return 'Turno completo';
                        if ($tieneVencido) return 'Sin salida ⚠️';
                        if ($tieneIngreso) return 'En puesto';

                        if ($now->between($horaInicio, $horaFin)) return 'Sin escanear';
                        if ($now->lt($horaInicio))                 return 'Pendiente';

                        return 'Ausente';
                    })
                    ->color(fn ($state) => match ($state) {
                        'Turno completo' => 'success',
                        'En puesto'      => 'info',
                        'Sin salida ⚠️'  => 'warning',
                        'Sin escanear'   => 'danger',
                        'Pendiente'      => 'warning',
                        'Ausente'        => 'danger',
                        default          => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
