<x-filament-panels::page>

    <style>
        .stats-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stats-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .stats-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .stats-grid-2 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
        @media (max-width: 480px) {
            .stats-grid-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
    </style>

    {{-- Selector de equipo --}}
    <x-filament::section>
        {{ $this->form }}
    </x-filament::section>

    <div class="mt-6">

    @if (! $equipoId)
        {{-- ════════════════ VISTA GRUPAL ════════════════ --}}

        @php $ranking = $this->getRankingEquipos(); @endphp

        <div class="stats-grid-4">
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Equipos con reportes</p>
                <p class="text-3xl font-semibold mt-2">{{ $ranking->count() }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total reportes</p>
                <p class="text-3xl font-semibold mt-2">{{ $ranking->sum('total') }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pendientes totales</p>
                <p class="text-3xl font-semibold mt-2 text-danger-600 dark:text-danger-400">{{ $ranking->sum('pendientes') }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">% Resuelto promedio</p>
                <p class="text-3xl font-semibold mt-2 text-success-600 dark:text-success-400">
                    {{ $ranking->count() > 0 ? round($ranking->avg('pct_resuelto')) : 0 }}%
                </p>
            </div>
        </div>

        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <p class="text-sm font-semibold">🏆 Ranking de equipos — más problemáticos primero</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left">Equipo</th>
                        <th class="px-4 py-2 text-center">Total</th>
                        <th class="px-4 py-2 text-center">Pendientes</th>
                        <th class="px-4 py-2 text-center">En proceso</th>
                        <th class="px-4 py-2 text-center">Finalizados</th>
                        <th class="px-4 py-2 text-center">Días prom.</th>
                        <th class="px-4 py-2 text-center">% Resuelto</th>
                        <th class="px-4 py-2 text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ranking as $item)
                        <tr class="border-b border-gray-50 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-2.5 font-medium">{{ $item->nombre }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800">
                                    {{ $item->total }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if ($item->pendientes > 0)
                                    <span class="inline-flex items-center justify-center min-w-[28px] px-2 py-0.5 rounded-full text-xs font-semibold bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                                        {{ $item->pendientes }}
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">{{ $item->en_proceso }}</td>
                            <td class="px-4 py-2.5 text-center text-success-600 dark:text-success-400 font-medium">{{ $item->finalizados }}</td>
                            <td class="px-4 py-2.5 text-center">{{ $item->dias_promedio ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @php
                                    $color = match(true) {
                                        $item->pct_resuelto >= 80 => 'text-success-600 dark:text-success-400',
                                        $item->pct_resuelto >= 50 => 'text-warning-600 dark:text-warning-400',
                                        default => 'text-danger-600 dark:text-danger-400',
                                    };
                                @endphp
                                <span class="font-semibold {{ $color }}">{{ $item->pct_resuelto }}%</span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <button
                                    wire:click="$set('equipoId', {{ $item->id }})"
                                    class="text-primary-600 hover:text-primary-700 dark:text-primary-400 text-xs font-medium underline"
                                >
                                    Ver historial →
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Sin reportes técnicos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        {{-- ════════════════ VISTA INDIVIDUAL ════════════════ --}}

        @php
            $equipo = $this->getEquipoSeleccionado();
            $stats  = $this->getStatsEquipo();
            $historial = $this->getHistorialEquipo();
        @endphp

        <div class="mb-4">
            <button
                wire:click="$set('equipoId', null)"
                class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium flex items-center gap-1"
            >
                ← Volver al ranking general
            </button>
        </div>

        {{-- Tarjetas del equipo --}}
        <div class="stats-grid-4">
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total reportes</p>
                <p class="text-3xl font-semibold mt-2">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Última falla</p>
                <p class="text-xl font-semibold mt-2">
                    {{ $stats['ultima_falla'] ? \Carbon\Carbon::parse($stats['ultima_falla'])->format('d/m/Y') : '—' }}
                </p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">Días prom. resolución</p>
                <p class="text-3xl font-semibold mt-2">{{ $stats['dias_promedio'] ?? '—' }}</p>
            </div>
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">% Resuelto</p>
                <p class="text-3xl font-semibold mt-2 {{ $stats['pct_resuelto'] >= 80 ? 'text-success-600 dark:text-success-400' : ($stats['pct_resuelto'] >= 50 ? 'text-warning-600 dark:text-warning-400' : 'text-danger-600 dark:text-danger-400') }}">
                    {{ $stats['pct_resuelto'] }}%
                </p>
            </div>
        </div>

        {{-- Tendencia + tipo de intervención --}}
        <div class="stats-grid-2">
            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Tendencia (últimos 6 meses)</p>
                @php
                    $icono = match($stats['tendencia']) {
                        'empeorando' => ['heroicon-m-arrow-trending-up', 'text-danger-500', '⚠️ Empeorando'],
                        'mejorando'  => ['heroicon-m-arrow-trending-down', 'text-success-500', '✅ Mejorando'],
                        default      => ['heroicon-m-minus', 'text-gray-400', '➖ Estable'],
                    };
                @endphp
                <div class="flex items-center gap-3">
                    <x-filament::icon :icon="$icono[0]" class="w-8 h-8 {{ $icono[1] }}" />
                    <div>
                        <p class="font-semibold {{ $icono[1] }}">{{ $icono[2] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Últimos 3 meses: {{ $stats['recientes'] }} · Anteriores 3 meses: {{ $stats['anteriores'] }}
                        </p>
                    </div>
                </div>
                @if ($stats['tendencia'] === 'empeorando')
                    <p class="text-xs text-danger-600 dark:text-danger-400 mt-2">
                        Este equipo está fallando con más frecuencia. Considera evaluar reemplazo.
                    </p>
                @endif
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-900 p-4 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Tipo de intervención</p>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Preventivas</span>
                        <span class="font-semibold">{{ $stats['preventivos'] }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-success-500" style="width: {{ $stats['total'] > 0 ? round(($stats['preventivos']/$stats['total'])*100) : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span>Correctivas</span>
                        <span class="font-semibold">{{ $stats['correctivos'] }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-warning-500" style="width: {{ $stats['total'] > 0 ? round(($stats['correctivos']/$stats['total'])*100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial completo --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <p class="text-sm font-semibold">📋 Historial completo — {{ $equipo->tipoEquipo->descripcion ?? '' }} {{ $equipo->descripcion }}</p>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Tipo intervención</th>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-center">Estado</th>
                        <th class="px-4 py-2 text-left">Atendido por</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historial as $r)
                        <tr class="border-b border-gray-50 dark:border-gray-800 last:border-0">
                            <td class="px-4 py-2.5">{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-block px-2 py-0.5 rounded text-xs bg-info-50 text-info-700 dark:bg-info-900/30 dark:text-info-400">
                                    {{ $r->tipoIntervencion->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 max-w-xs truncate" title="{{ $r->descripcion }}">{{ $r->descripcion }}</td>
                            <td class="px-4 py-2.5 text-center">
                                @php
                                    $estadoNombre = $r->ultimoEstado->estadoReporte->nombre ?? '—';
                                    $colorEstado = match($estadoNombre) {
                                        'Pendiente'  => 'bg-danger-50 text-danger-700 dark:bg-danger-900/30 dark:text-danger-400',
                                        'En proceso' => 'bg-warning-50 text-warning-700 dark:bg-warning-900/30 dark:text-warning-400',
                                        default      => 'bg-success-50 text-success-700 dark:bg-success-900/30 dark:text-success-400',
                                    };
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded text-xs {{ $colorEstado }}">{{ $estadoNombre }}</span>
                            </td>
                            <td class="px-4 py-2.5">{{ $r->subidopor ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin reportes para este equipo</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif

    </div>

</x-filament-panels::page>
