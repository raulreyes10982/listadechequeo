@php
    $data = $this->getData();
    $total = $data['total'];
    $uid = $this->getId();
    $periodoLabel = match($data['periodo']) {
        'ayer'        => 'Ayer',
        'semanal'     => 'Esta semana',
        'mensual'     => 'Este mes',
        'trimestral'  => 'Último trimestre',
        'semestral'   => 'Último semestre',
        'anual'       => 'Este año',
        'personalizado' => 'Período personalizado',
        default       => 'Todos',
    };
@endphp

<div wire:key="dona-widget-{{ $uid }}" wire:ignore.self>
<style>
.rd-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: 14px;
}
@media(max-width: 768px) { .rd-grid { grid-template-columns: 1fr; } }
.rd-card {
    background: white;
    border-radius: 12px;
    border: 0.5px solid #e5e7eb;
    padding: 16px 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
}
.dark .rd-card { background: #1f2937; border-color: #374151; }
.rd-title { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 2px; }
.dark .rd-title { color: #f9fafb; }
.rd-sub { font-size: 11px; color: #9ca3af; margin-bottom: 14px; }
.rd-inner { display: flex; align-items: center; gap: 16px; }
.rd-wrap { position: relative; width: 130px; height: 130px; flex-shrink: 0; }
.rd-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    pointer-events: none;
}
.rd-center-num { font-size: 22px; font-weight: 700; color: #111827; line-height: 1; }
.dark .rd-center-num { color: #f9fafb; }
.rd-center-lbl { font-size: 10px; color: #9ca3af; margin-top: 2px; }
.rd-legend { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.rd-row { display: flex; align-items: center; justify-content: space-between; font-size: 12px; gap: 6px; }
.rd-left { display: flex; align-items: center; gap: 6px; color: #6b7280; min-width: 0; }
.dark .rd-left { color: #9ca3af; }
.rd-sq { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
.rd-lname { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90px; }
.rd-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.rd-pct { font-weight: 600; font-size: 12px; color: #111827; min-width: 36px; text-align: right; }
.dark .rd-pct { color: #f9fafb; }
.rd-cnt { font-size: 10px; color: #9ca3af; }
.rd-empty { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; }
.rd-update { font-size: 10px; color: #9ca3af; margin-top: 12px; display: flex; align-items: center; gap: 4px; }
</style>

<div class="rd-grid">

    {{-- DONA 1: Por estado --}}
    <div class="rd-card">
        <div class="rd-title">Distribución por estado</div>
        <div class="rd-sub">({{ $periodoLabel }})</div>
        @if($data['estados']->isEmpty())
            <div class="rd-empty">Sin reportes en este período</div>
        @else
            <div class="rd-inner">
                <div class="rd-wrap">
                    <canvas id="dona-e-{{ $uid }}" width="130" height="130"></canvas>
                    <div class="rd-center">
                        <div class="rd-center-num">{{ $total }}</div>
                        <div class="rd-center-lbl">Total</div>
                    </div>
                </div>
                <div class="rd-legend">
                    @foreach($data['estados'] as $e)
                        <div class="rd-row">
                            <span class="rd-left">
                                <span class="rd-sq" style="background:{{ $e['color'] }}"></span>
                                <span class="rd-lname" title="{{ $e['label'] }}">{{ $e['label'] }}</span>
                            </span>
                            <span class="rd-right">
                                <span class="rd-pct">{{ $e['pct'] }}%</span>
                                <span class="rd-cnt">({{ $e['count'] }})</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="rd-update">↻ Actualización automática · 2 min</div>
    </div>

    {{-- DONA 2: Por prioridad --}}
    <div class="rd-card">
        <div class="rd-title">Distribución por prioridad</div>
        <div class="rd-sub">({{ $periodoLabel }})</div>
        @if($data['prioridades']->isEmpty())
            <div class="rd-empty">Sin reportes en este período</div>
        @else
            <div class="rd-inner">
                <div class="rd-wrap">
                    <canvas id="dona-p-{{ $uid }}" width="130" height="130"></canvas>
                    <div class="rd-center">
                        <div class="rd-center-num">{{ $total }}</div>
                        <div class="rd-center-lbl">Total</div>
                    </div>
                </div>
                <div class="rd-legend">
                    @foreach($data['prioridades'] as $p)
                        <div class="rd-row">
                            <span class="rd-left">
                                <span class="rd-sq" style="background:{{ $p['color'] }}"></span>
                                <span class="rd-lname" title="{{ $p['label'] }}">{{ $p['label'] }}</span>
                            </span>
                            <span class="rd-right">
                                <span class="rd-pct">{{ $p['pct'] }}%</span>
                                <span class="rd-cnt">({{ $p['count'] }})</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="rd-update">↻ Actualización automática · 2 min</div>
    </div>

    {{-- DONA 3: Por categoría --}}
    <div class="rd-card">
        <div class="rd-title">Distribución por categoría</div>
        <div class="rd-sub">({{ $periodoLabel }})</div>
        @if($data['categorias']->isEmpty())
            <div class="rd-empty">Sin reportes en este período</div>
        @else
            <div class="rd-inner">
                <div class="rd-wrap">
                    <canvas id="dona-c-{{ $uid }}" width="130" height="130"></canvas>
                    <div class="rd-center">
                        <div class="rd-center-num">{{ $total }}</div>
                        <div class="rd-center-lbl">Total</div>
                    </div>
                </div>
                <div class="rd-legend">
                    @foreach($data['categorias'] as $c)
                        <div class="rd-row">
                            <span class="rd-left">
                                <span class="rd-sq" style="background:{{ $c['color'] }}"></span>
                                <span class="rd-lname" title="{{ $c['label'] }}">{{ $c['label'] }}</span>
                            </span>
                            <span class="rd-right">
                                <span class="rd-pct">{{ $c['pct'] }}%</span>
                                <span class="rd-cnt">({{ $c['count'] }})</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="rd-update">↻ Actualización automática · 2 min</div>
    </div>

</div>

{{-- Chart.js + inicialización robusta con Livewire --}}
<script>
(function iniciarDonas() {

    const uid = '{{ $uid }}';

    const datasets = {
        e: {
            labels: @json($data['estados']->pluck('label')),
            data:   @json($data['estados']->pluck('pct')),
            colors: @json($data['estados']->pluck('color')),
        },
        p: {
            labels: @json($data['prioridades']->pluck('label')),
            data:   @json($data['prioridades']->pluck('pct')),
            colors: @json($data['prioridades']->pluck('color')),
        },
        c: {
            labels: @json($data['categorias']->pluck('label')),
            data:   @json($data['categorias']->pluck('pct')),
            colors: @json($data['categorias']->pluck('color')),
        },
    };

    function dibujar() {
        // ✅ Esperar a que Chart.js esté disponible
        if (typeof Chart === 'undefined') {
            setTimeout(dibujar, 200);
            return;
        }

        ['e', 'p', 'c'].forEach(key => {
            const el = document.getElementById('dona-' + key + '-' + uid);
            if (!el || !datasets[key].data.length) return;

            // Destruir instancia anterior si existe (re-render de Livewire)
            const existing = Chart.getChart(el);
            if (existing) existing.destroy();

            new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: datasets[key].labels,
                    datasets: [{
                        data:            datasets[key].data,
                        backgroundColor: datasets[key].colors,
                        borderWidth:     2,
                        borderColor:     'transparent',
                        hoverOffset:     6,
                    }]
                },
                options: {
                    responsive:          false, // ← false para evitar conflictos con Livewire
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                            }
                        }
                    },
                    animation: { duration: 400 }
                }
            });
        });
    }

    // ✅ Cargar Chart.js si no está, luego dibujar
    if (typeof Chart === 'undefined') {
        const s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';
        s.onload = () => setTimeout(dibujar, 100);
        document.head.appendChild(s);
    } else {
        // Ya cargado — esperar al próximo tick para que el DOM esté listo
        setTimeout(dibujar, 100);
    }

    // ✅ Re-dibujar cuando Livewire actualice el componente
    document.addEventListener('livewire:update', () => setTimeout(dibujar, 150));

})();
</script>

</div>
