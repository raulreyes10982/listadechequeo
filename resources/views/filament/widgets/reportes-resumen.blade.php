<style>
    .rr-grid-1 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
    .rr-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 1rem; }
    .rr-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 1rem; }
    .rr-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 1rem; }
    @media (max-width: 768px) {
        .rr-grid-4 { grid-template-columns: repeat(2, minmax(0,1fr)); }
        .rr-grid-3 { grid-template-columns: repeat(1, minmax(0,1fr)); }
    }
    @media (max-width: 480px) {
        .rr-grid-2 { grid-template-columns: repeat(1, minmax(0,1fr)); }
        .rr-grid-4 { grid-template-columns: repeat(1, minmax(0,1fr)); }
    }
</style>

@php $data = $this->getData(); @endphp

<div class="fi-wi-stats-overview rr-grid-1">

    {{-- Fila 1: Totales --}}
    <div class="rr-grid-4">
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total reportes</span>
            <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $data['total'] }}</div>
            <div class="mt-1 text-xs text-gray-400">Hoy: {{ $data['hoyCount'] }} nuevo(s)</div>
        </div>
        <div class="fi-wi-stats-overview-stat rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Críticos abiertos</span>
            <div class="mt-2 text-3xl font-semibold {{ $data['criticos'] > 0 ? 'text-danger-600 dark:text-danger-400' : 'text-success-600 dark:text-success-400' }}">
                {{ $data['criticos'] }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Prioridad alta sin cerrar</div>
        </div>
    </div>

    {{-- Fila 2: Tres columnas --}}
    <div class="rr-grid-3">

        {{-- Por estado --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Por estado</p>
            @forelse ($data['porEstado'] as $item)
                @php
                    $color = match(strtolower($item['nombre'])) {
                        'pendiente'  => 'bg-danger-500',
                        'en proceso' => 'bg-warning-500',
                        'finalizado', 'cerrado', 'resuelto' => 'bg-success-500',
                        default      => 'bg-gray-400',
                    };
                @endphp
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                    <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="inline-block w-2 h-2 rounded-full {{ $color }}"></span>
                        {{ $item['nombre'] }}
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['total'] }}</span>
                </div>
            @empty
                <p class="text-xs text-gray-400">Sin datos</p>
            @endforelse
        </div>

        {{-- Por categoría --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Por categoría</p>
            @php $maxCat = $data['porCategoria']->max('total') ?: 1; @endphp
            @forelse ($data['porCategoria'] as $item)
                <div class="py-1.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300 truncate">{{ $item['nombre'] }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white ml-2">{{ $item['total'] }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-500 rounded-full" style="width: {{ $maxCat > 0 ? round(($item['total']/$maxCat)*100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400">Sin datos</p>
            @endforelse
        </div>

        {{-- Por prioridad --}}
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-3">Por prioridad</p>
            @php $maxPrio = $data['porPrioridad']->max('total') ?: 1; @endphp
            @forelse ($data['porPrioridad'] as $item)
                @php
                    $color = match(strtolower($item['nombre'])) {
                        'alta', 'urgente', 'crítica', 'critica' => 'bg-danger-500',
                        'media'  => 'bg-warning-500',
                        'baja'   => 'bg-info-500',
                        default  => 'bg-gray-400',
                    };
                @endphp
                <div class="py-1.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                            <span class="inline-block w-2 h-2 rounded-full {{ $color }}"></span>
                            {{ $item['nombre'] }}
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $item['total'] }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $color }} rounded-full" style="width: {{ $maxPrio > 0 ? round(($item['total']/$maxPrio)*100) : 0 }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-gray-400">Sin datos</p>
            @endforelse
        </div>

    </div>
</div>
