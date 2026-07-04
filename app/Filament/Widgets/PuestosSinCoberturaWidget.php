<?php

namespace App\Filament\Widgets;

use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PuestosSinCoberturaWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '60s';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '🚨 Puestos sin cobertura ahora';

    public function table(Table $table): Table
    {
        $now  = Carbon::now();
        $hoy  = Carbon::today()->toDateString();
        $hora = $now->toTimeString();

        return $table
            ->query(
                RegistrarTurno::query()
                    ->with(['colaborador', 'puestoSeguridad', 'verificaciones'])
                    ->whereDate('fecha', $hoy)
                    ->whereTime('hora_inicio', '<=', $hora)
                    ->whereTime('hora_fin',    '>=', $hora)
                    ->whereDoesntHave('verificaciones', fn ($q) =>
                        $q->where('tipo', 'ingreso')->where('estado', 'verificado')
                    )
                    ->orderBy('hora_inicio')
            )
            ->columns([
                Tables\Columns\TextColumn::make('puesto')
                    ->label('Puesto sin cobertura')
                    ->getStateUsing(fn ($record) =>
                        ($record->puestoSeguridad->codigo ?? '—') .
                        ' — ' .
                        ($record->puestoSeguridad->puesto ?? '—')
                    ),

                Tables\Columns\TextColumn::make('guardia_asignado')
                    ->label('Guardia asignado')
                    ->getStateUsing(fn ($record) =>
                        trim(($record->colaborador->nombre ?? '') . ' ' . ($record->colaborador->apellido ?? ''))
                    ),

                Tables\Columns\TextColumn::make('hora_inicio')
                    ->label('Debía ingresar a las')
                    ->alignment('center')
                    ->getStateUsing(fn ($record) =>
                        Carbon::parse($record->hora_inicio)->format('H:i')
                    ),

                Tables\Columns\TextColumn::make('tiempo_sin_cobertura')
                    ->label('Tiempo sin cubrir')
                    ->alignment('center')
                    ->badge()
                    ->color('danger')
                    ->getStateUsing(function ($record) use ($now) {
                        $inicio = Carbon::parse($record->hora_inicio);
                        $minutos = $inicio->diffInMinutes($now);
                        if ($minutos < 60) return "{$minutos} min";
                        $h = intdiv($minutos, 60);
                        $m = $minutos % 60;
                        return "{$h}h {$m}m";
                    }),
            ])
            ->emptyStateHeading('✅ Todos los puestos tienen cobertura')
            ->emptyStateDescription('No hay puestos activos sin guardia en este momento.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->paginated(false);
    }
}
