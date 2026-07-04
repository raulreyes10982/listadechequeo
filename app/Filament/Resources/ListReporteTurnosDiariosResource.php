<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListReporteTurnosDiariosResource\Pages;
use App\Models\Colaborador;
use App\Models\RegistrarTurno;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ListReporteTurnosDiariosResource extends Resource
{
    protected static ?string $model = \App\Models\ListReporteTurnosDiarios::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup  = 'Programación';
    protected static ?string $navigationLabel  = 'Reportes de Seguridad';
    protected static ?string $modelLabel       = 'Reportes de Seguridad';
    protected static ?string $pluralModelLabel = 'Reportes de Seguridad';
    protected static ?int    $navigationSort   = 5;

    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }

    /*
    |--------------------------------------------------------------------------
    | QUERY — agrupa por fecha con estadísticas
    |--------------------------------------------------------------------------
    */
    public static function getEloquentQuery(): Builder
    {
        return RegistrarTurno::query()
            ->selectRaw("
                MIN(id)  AS id,
                fecha,
                COUNT(*) AS total_guardias,
                SUM(
                    CASE WHEN EXISTS (
                        SELECT 1 FROM verificacion_turnos vt
                        WHERE vt.registrar_turno_id = registrar_turnos.id
                            AND vt.tipo   = 'ingreso'
                            AND vt.estado = 'verificado'
                    ) THEN 1 ELSE 0 END
                ) AS con_ingreso,
                SUM(
                    CASE WHEN EXISTS (
                        SELECT 1 FROM verificacion_turnos vt
                        WHERE vt.registrar_turno_id = registrar_turnos.id
                            AND vt.tipo   = 'salida'
                            AND vt.estado = 'verificado'
                    ) THEN 1 ELSE 0 END
                ) AS con_salida
            ")
            ->groupBy('fecha')
            ->orderByDesc('fecha');
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
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->formatStateUsing(fn ($state) =>
                        Carbon::parse($state)->translatedFormat('l, d \d\e F \d\e Y')
                    )
                    ->sortable()->searchable()->alignment('center'),

                Tables\Columns\TextColumn::make('total_guardias')
                    ->label('Guardias')->alignment('center')->badge()->color('info'),

                Tables\Columns\TextColumn::make('con_ingreso')
                    ->label('Ingreso verificado')->alignment('center')->badge()
                    ->color(fn ($record) =>
                        $record->con_ingreso == $record->total_guardias ? 'success' : 'warning'
                    ),

                Tables\Columns\TextColumn::make('con_salida')
                    ->label('Salida verificada')->alignment('center')->badge()
                    ->color(fn ($record) =>
                        $record->con_salida == $record->total_guardias ? 'success' : 'danger'
                    ),

                Tables\Columns\TextColumn::make('estado_dia')
                    ->label('Estado del día')->alignment('center')->badge()
                    ->getStateUsing(fn ($record) => match (true) {
                        $record->con_salida == $record->total_guardias => 'Completo',
                        $record->con_ingreso > 0                       => 'En curso',
                        default                                        => 'Sin verificar',
                    })
                    ->color(fn ($state) => match ($state) {
                        'Completo'      => 'success',
                        'En curso'      => 'warning',
                        'Sin verificar' => 'danger',
                        default         => 'gray',
                    }),
            ])

            ->actions([
                // Descargar PDF del día completo
                Action::make('descargarPdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->tooltip(fn ($record) =>
                        'personal de seguridad ' .
                        Carbon::parse($record->fecha)->format('d-m-Y') . '.pdf'
                    )
                    ->action(fn ($record) => static::generarPdfDia($record->fecha)),
            ])

            ->filters([
                // Filtro por rango de fechas
                Tables\Filters\Filter::make('rango_fechas')
                    ->label('Rango de fechas')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(fn (Builder $q, array $data) =>
                        $q->when($data['desde'], fn ($q, $d) => $q->whereDate('fecha', '>=', $d))
                            ->when($data['hasta'], fn ($q, $d) => $q->whereDate('fecha', '<=', $d))
                    ),
            ])

            // ─── Botón superior: reporte individual por guardia ────────────
            ->headerActions([
                Action::make('reporteGuardia')
                    ->label('Reporte por Guardia')
                    ->icon('heroicon-o-user-circle')
                    ->color('primary')
                    ->modalHeading('Reporte de puestos por guardia')
                    ->modalDescription('Selecciona el guardia y el período para descargar su historial de puestos.')
                    ->modalWidth('md')
                    ->modalSubmitActionLabel('Descargar PDF')
                    ->form([
                        Select::make('colaborador_id')
                            ->label('Guardia de seguridad')
                            ->options(
                                Colaborador::orderBy('nombre')->get()
                                    ->mapWithKeys(fn ($c) => [
                                        $c->id => $c->nombre . ' ' . $c->apellido,
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->placeholder('Buscar guardia...'),

                        DatePicker::make('fecha_desde')
                            ->label('Desde')
                            ->native(false)
                            ->required()
                            ->default(now()->startOfMonth()->toDateString()),

                        DatePicker::make('fecha_hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->required()
                            ->default(now()->toDateString()),
                    ])
                    ->action(fn (array $data) =>
                        static::generarPdfGuardia(
                            $data['colaborador_id'],
                            $data['fecha_desde'],
                            $data['fecha_hasta']
                        )
                    ),
            ])

            ->bulkActions([])
            ->emptyStateHeading('No hay turnos registrados')
            ->emptyStateDescription('Cuando se registren turnos aparecerán aquí agrupados por día.');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF 1 — Todos los guardias del día
    | Nombre: "personal de seguridad dd-mm-aaaa.pdf"
    |--------------------------------------------------------------------------
    */
    public static function generarPdfDia(string $fecha)
    {
        $turnos = RegistrarTurno::with(['colaborador', 'puestoSeguridad', 'verificaciones'])
            ->whereDate('fecha', $fecha)
            ->orderBy('hora_inicio')
            ->get();

        if ($turnos->isEmpty()) {
            Notification::make()
                ->title('Sin datos')
                ->body('No hay turnos para el día ' . Carbon::parse($fecha)->format('d/m/Y'))
                ->warning()->send();
            return;
        }

        $filas   = static::construirFilas($turnos);
        $resumen = static::calcularResumen($turnos, $filas);

        $pdf = Pdf::loadView('filament.pdf.reporte-diario-turnos', array_merge([
            'fecha'       => $fecha,
            'turnos'      => $filas,
            'generadoPor' => Auth::user()?->name ?? 'Sistema',
        ], $resumen))->setPaper('a4', 'landscape');

        return Response::streamDownload(
            fn () => print($pdf->output()),
            'personal de seguridad ' . Carbon::parse($fecha)->format('d-m-Y') . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PDF 2 — Historial de un guardia en un período
    | Nombre: "guardia [Nombre Apellido] dd-mm-aaaa al dd-mm-aaaa.pdf"
    |--------------------------------------------------------------------------
    */
    public static function generarPdfGuardia(int $colaboradorId, string $desde, string $hasta)
    {
        $colaborador = Colaborador::find($colaboradorId);

        if (! $colaborador) {
            Notification::make()->title('Guardia no encontrado')->danger()->send();
            return;
        }

        $turnos = RegistrarTurno::with(['colaborador', 'puestoSeguridad', 'verificaciones'])
            ->where('colaborador_id', $colaboradorId)
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        if ($turnos->isEmpty()) {
            Notification::make()
                ->title('Sin datos')
                ->body("No hay turnos para {$colaborador->nombre} {$colaborador->apellido} en ese período.")
                ->warning()->send();
            return;
        }

        $filas   = static::construirFilas($turnos);
        $resumen = static::calcularResumen($turnos, $filas);

        $pdf = Pdf::loadView('filament.pdf.reporte-guardia-periodo', array_merge([
            'colaborador' => $colaborador,
            'desde'       => $desde,
            'hasta'       => $hasta,
            'turnos'      => $filas,
            'generadoPor' => Auth::user()?->name ?? 'Sistema',
        ], $resumen))->setPaper('a4', 'landscape');

        $nombre    = $colaborador->nombre . ' ' . $colaborador->apellido;
        $desdeStr  = Carbon::parse($desde)->format('d-m-Y');
        $hastaStr  = Carbon::parse($hasta)->format('d-m-Y');

        return Response::streamDownload(
            fn () => print($pdf->output()),
            "guardia {$nombre} {$desdeStr} al {$hastaStr}.pdf"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    protected static function construirFilas($turnos): \Illuminate\Support\Collection
    {
        return $turnos->map(function (RegistrarTurno $turno) {
            $ingreso = $turno->verificaciones
                ->where('tipo', 'ingreso')->where('estado', 'verificado')
                ->sortBy('hora_verificacion')->first();

            $salida = $turno->verificaciones
                ->where('tipo', 'salida')->where('estado', 'verificado')
                ->sortBy('hora_verificacion')->first();

            $horasTrabajadas = '—';
            if ($ingreso?->hora_verificacion && $salida?->hora_verificacion) {
                $min = $ingreso->hora_verificacion->diffInMinutes($salida->hora_verificacion);
                $horasTrabajadas = intdiv($min, 60) . 'h ' . str_pad($min % 60, 2, '0', STR_PAD_LEFT) . 'm';
            } elseif ($turno->hora_inicio && $turno->hora_fin) {
                $ini = Carbon::parse($turno->hora_inicio);
                $fin = Carbon::parse($turno->hora_fin);
                if ($fin->lessThan($ini)) $fin->addDay();
                $min = $ini->diffInMinutes($fin);
                $horasTrabajadas = '(prog.) ' . intdiv($min, 60) . 'h ' . str_pad($min % 60, 2, '0', STR_PAD_LEFT) . 'm';
            }

            return [
                'fecha'            => Carbon::parse($turno->fecha)->format('d/m/Y'),
                'nombre'           => $turno->colaborador->nombre . ' ' . $turno->colaborador->apellido,
                'puesto'           => $turno->puestoSeguridad->puesto,
                'codigo_puesto'    => $turno->puestoSeguridad->codigo,
                'hora_inicio_prog' => $turno->hora_inicio ? Carbon::parse($turno->hora_inicio)->format('H:i') : '—',
                'hora_fin_prog'    => $turno->hora_fin    ? Carbon::parse($turno->hora_fin)->format('H:i')    : '—',
                'hora_ingreso'     => $ingreso?->hora_verificacion?->format('H:i'),
                'hora_salida'      => $salida?->hora_verificacion?->format('H:i'),
                'horas_trabajadas' => $horasTrabajadas,
                'observacion'      => $turno->observacion,
            ];
        });
    }

    protected static function calcularResumen($turnos, $filas): array
    {
        $completos = $filas->filter(fn ($f) => $f['hora_ingreso'] && $f['hora_salida'])->count();
        $sinSalida = $filas->filter(fn ($f) => $f['hora_ingreso'] && ! $f['hora_salida'])->count();

        $minTotal = $turnos->sum(function ($t) {
            $i = $t->verificaciones->where('tipo','ingreso')->where('estado','verificado')
                ->sortBy('hora_verificacion')->first();
            $s = $t->verificaciones->where('tipo','salida')->where('estado','verificado')
                ->sortBy('hora_verificacion')->first();
            return ($i?->hora_verificacion && $s?->hora_verificacion)
                ? $i->hora_verificacion->diffInMinutes($s->hora_verificacion) : 0;
        });

        return [
            'totalGuardias'  => $filas->count(),
            'totalCompletos' => $completos,
            'totalSinSalida' => $sinSalida,
            'totalHoras'     => $minTotal > 0
                ? intdiv($minTotal, 60) . 'h ' . str_pad($minTotal % 60, 2, '0', STR_PAD_LEFT) . 'm'
                : '0h',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListReporteTurnosDiarios::route('/'),
        ];
    }
}
