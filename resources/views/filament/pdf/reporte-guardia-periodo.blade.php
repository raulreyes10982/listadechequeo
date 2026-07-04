<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de Puestos — {{ $colaborador->nombre }} {{ $colaborador->apellido }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; padding: 24px 28px; }

        /* ENCABEZADO */
        .header { border-bottom: 3px solid #1e3a5f; padding-bottom: 10px; margin-bottom: 18px; }
        .header-top { display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: middle; width: 65%; }
        .header-right { display: table-cell; vertical-align: middle; width: 35%; text-align: right; }
        .titulo { font-size: 16px; font-weight: bold; color: #1e3a5f; text-transform: uppercase; }
        .subtitulo { font-size: 11px; color: #444; margin-top: 3px; }
        .guardia-badge {
            display: inline-block; background: #1e3a5f; color: #fff;
            padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;
        }
        .periodo-badge {
            display: block; margin-top: 5px; font-size: 10px; color: #555; text-align: right;
        }

        /* RESUMEN */
        .resumen { display: table; width: 100%; margin-bottom: 16px; border: 1px solid #d0d7e2; border-radius: 4px; background: #f4f7fb; }
        .resumen-item { display: table-cell; padding: 8px 14px; text-align: center; border-right: 1px solid #d0d7e2; }
        .resumen-item:last-child { border-right: none; }
        .resumen-num { font-size: 20px; font-weight: bold; color: #1e3a5f; }
        .resumen-lbl { font-size: 9px; color: #666; text-transform: uppercase; margin-top: 2px; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #1e3a5f; color: #fff; }
        thead th { padding: 7px 8px; text-align: center; font-size: 10px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f0f4fa; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #dde3ec; vertical-align: middle; }

        .col-num   { text-align: center; width: 4%; color: #888; }
        .col-fecha { text-align: center; width: 12%; font-weight: 500; }
        .col-puesto { width: 22%; }
        .col-hora  { text-align: center; width: 10%; }
        .col-horas { text-align: center; width: 10%; font-weight: bold; }
        .col-obs   { width: 20%; color: #555; font-size: 10px; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-completo   { background: #d4edda; color: #155724; }
        .badge-sin-salida { background: #f8d7da; color: #721c24; }
        .badge-sin-ingreso { background: #fff3cd; color: #856404; }

        .hora-ingreso { color: #0d6efd; font-weight: bold; }
        .hora-salida  { color: #198754; font-weight: bold; }
        .hora-vacia   { color: #aaa; font-style: italic; }

        /* FOOTER */
        .footer { border-top: 1px solid #ccc; padding-top: 8px; margin-top: 10px; display: table; width: 100%; font-size: 9px; color: #888; }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }

        /* FIRMAS */
        .firmas { display: table; width: 100%; margin-top: 30px; }
        .firma-col { display: table-cell; width: 33%; text-align: center; padding: 0 10px; }
        .firma-linea { border-top: 1px solid #555; margin: 0 auto; width: 85%; margin-top: 40px; }
        .firma-label { font-size: 9px; color: #555; margin-top: 4px; }

        .sin-datos { text-align: center; padding: 30px; color: #888; font-style: italic; }
    </style>
</head>
<body>

    {{-- ENCABEZADO --}}
    <div class="header">
        <div class="header-top">
            <div class="header-left">
                <div class="titulo">Historial de Puestos — Personal de Seguridad</div>
                <div class="subtitulo">Registro de turnos verificados por guardia en el período seleccionado</div>
            </div>
            <div class="header-right">
                <span class="guardia-badge">
                    {{ $colaborador->nombre }} {{ $colaborador->apellido }}
                </span>
                <span class="periodo-badge">
                    Del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                    al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- RESUMEN --}}
    <div class="resumen">
        <div class="resumen-item">
            <div class="resumen-num">{{ $totalGuardias }}</div>
            <div class="resumen-lbl">Turnos en el período</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num">{{ $totalCompletos }}</div>
            <div class="resumen-lbl">Turnos completos</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num">{{ $totalSinSalida }}</div>
            <div class="resumen-lbl">Sin salida registrada</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num">{{ $totalHoras }}</div>
            <div class="resumen-lbl">Horas totales trabajadas</div>
        </div>
        <div class="resumen-item">
            <div class="resumen-num">{{ $generadoPor }}</div>
            <div class="resumen-lbl">Generado por</div>
        </div>
    </div>

    {{-- TABLA --}}
    @if(count($turnos) === 0)
        <div class="sin-datos">No hay turnos registrados para este guardia en el período seleccionado.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="col-num">#</th>
                    <th class="col-fecha">Fecha</th>
                    <th class="col-puesto">Puesto</th>
                    <th class="col-hora">Turno prog.<br>Inicio / Fin</th>
                    <th class="col-hora">Ingreso<br>verificado</th>
                    <th class="col-hora">Salida<br>verificada</th>
                    <th class="col-horas">Horas<br>trabajadas</th>
                    <th class="col-num">Estado</th>
                    <th class="col-obs">Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($turnos as $i => $fila)
                <tr>
                    <td class="col-num">{{ $i + 1 }}</td>

                    <td class="col-fecha">{{ $fila['fecha'] }}</td>

                    <td class="col-puesto">
                        <strong>{{ $fila['codigo_puesto'] }}</strong><br>
                        <span style="font-size:9px;color:#555">{{ $fila['puesto'] }}</span>
                    </td>

                    <td class="col-hora" style="font-size:10px;">
                        {{ $fila['hora_inicio_prog'] }}<br>
                        {{ $fila['hora_fin_prog'] }}
                    </td>

                    <td class="col-hora">
                        @if($fila['hora_ingreso'])
                            <span class="hora-ingreso">{{ $fila['hora_ingreso'] }}</span>
                        @else
                            <span class="hora-vacia">—</span>
                        @endif
                    </td>

                    <td class="col-hora">
                        @if($fila['hora_salida'])
                            <span class="hora-salida">{{ $fila['hora_salida'] }}</span>
                        @else
                            <span class="hora-vacia">—</span>
                        @endif
                    </td>

                    <td class="col-horas">{{ $fila['horas_trabajadas'] }}</td>

                    <td class="col-num">
                        @if($fila['hora_ingreso'] && $fila['hora_salida'])
                            <span class="badge badge-completo">Completo</span>
                        @elseif($fila['hora_ingreso'])
                            <span class="badge badge-sin-salida">Sin salida</span>
                        @else
                            <span class="badge badge-sin-ingreso">Sin ingreso</span>
                        @endif
                    </td>

                    <td class="col-obs">{{ $fila['observacion'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FIRMAS --}}
    <div class="firmas">
        <div class="firma-col">
            <div class="firma-linea"></div>
            <div class="firma-label">Supervisor de Turno</div>
        </div>
        <div class="firma-col">
            <div class="firma-linea"></div>
            <div class="firma-label">Jefe de Seguridad</div>
        </div>
        <div class="firma-col">
            <div class="firma-linea"></div>
            <div class="firma-label">Revisado por</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            Generado el {{ now()->format('d/m/Y H:i') }} — Sistema de Seguridad
        </div>
        <div class="footer-right">Página 1</div>
    </div>

</body>
</html>
