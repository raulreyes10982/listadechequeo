@php $data = $this->getData(); @endphp

<style>
.rr-cards { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 12px; margin-bottom: 4px; }
@media(max-width:900px) { .rr-cards { grid-template-columns: repeat(3, minmax(0,1fr)); } }
@media(max-width:600px) { .rr-cards { grid-template-columns: repeat(2, minmax(0,1fr)); } }

.rr-card {
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    position: relative;
    overflow: hidden;
    user-select: none;
}
.dark .rr-card { background: rgb(30,30,30); }

.rr-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

/* Activo */
.rr-card-active { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }
/* Inactivo — opaco cuando otro está seleccionado */
.rr-card-dim { opacity: .4; filter: grayscale(.3); }

/* Colores por tipo */
.rr-card-total   { border-color: #6366f1; }
.rr-card-total .rr-num { color: #6366f1; }
.rr-card-critico { border-color: #ef4444; }
.rr-card-critico .rr-num { color: #ef4444; }
.rr-card-pend    { border-color: #f59e0b; }
.rr-card-pend .rr-num { color: #f59e0b; }
.rr-card-proc    { border-color: #3b82f6; }
.rr-card-proc .rr-num { color: #3b82f6; }
.rr-card-fin     { border-color: #22c55e; }
.rr-card-fin .rr-num { color: #22c55e; }

.rr-label { font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 500; }
.dark .rr-label { color: #9ca3af; }
.rr-num { font-size: 28px; font-weight: 700; line-height: 1; margin-bottom: 4px; }
.rr-sub { font-size: 11px; color: #9ca3af; }

/* Indicador seleccionado */
.rr-card-active::after {
    content: '✓';
    position: absolute;
    top: 8px; right: 10px;
    font-size: 13px;
    font-weight: 700;
    color: inherit;
    opacity: .6;
}
/* Barra de % */
.rr-pct-bar { height: 4px; background: #f3f4f6; border-radius: 2px; margin-top: 8px; overflow: hidden; }
.dark .rr-pct-bar { background: #374151; }
.rr-pct-fill { height: 100%; background: #22c55e; border-radius: 2px; transition: width .4s ease; }
</style>

<div class="rr-cards">

    {{-- TOTAL --}}
    @php
        $isActive   = $data['filtroActivo'] === null;
        $hasFiltro  = $data['filtroActivo'] !== null;
    @endphp
    <div
        wire:click="seleccionarFiltro('all')"
        class="rr-card rr-card-total {{ $isActive ? '' : ($hasFiltro ? 'rr-card-dim' : '') }}"
    >
        <div class="rr-label">Total reportes</div>
        <div class="rr-num">{{ $data['total'] }}</div>
        <div class="rr-sub">Hoy: {{ $data['hoyCount'] }} nuevo(s)</div>
    </div>

    {{-- CRÍTICOS --}}
    @php $isActive = $data['filtroActivo'] === 'critico'; @endphp
    <div
        wire:click="seleccionarFiltro('critico')"
        class="rr-card rr-card-critico {{ $isActive ? 'rr-card-active' : ($hasFiltro ? 'rr-card-dim' : '') }}"
    >
        <div class="rr-label">Críticos</div>
        <div class="rr-num">{{ $data['criticos'] }}</div>
        <div class="rr-sub">Prioridad alta sin cerrar</div>
    </div>

    {{-- PENDIENTES --}}
    @php $isActive = $data['filtroActivo'] === 'pendiente'; @endphp
    <div
        wire:click="seleccionarFiltro('pendiente')"
        class="rr-card rr-card-pend {{ $isActive ? 'rr-card-active' : ($hasFiltro ? 'rr-card-dim' : '') }}"
    >
        <div class="rr-label">Pendientes</div>
        <div class="rr-num">{{ $data['pendientes'] }}</div>
        <div class="rr-sub">Sin atender</div>
    </div>

    {{-- EN PROCESO --}}
    @php $isActive = $data['filtroActivo'] === 'en_proceso'; @endphp
    <div
        wire:click="seleccionarFiltro('en_proceso')"
        class="rr-card rr-card-proc {{ $isActive ? 'rr-card-active' : ($hasFiltro ? 'rr-card-dim' : '') }}"
    >
        <div class="rr-label">En proceso</div>
        <div class="rr-num">{{ $data['enProceso'] }}</div>
        <div class="rr-sub">Siendo atendidos</div>
    </div>

    {{-- FINALIZADOS --}}
    @php $isActive = $data['filtroActivo'] === 'finalizado'; @endphp
    <div
        wire:click="seleccionarFiltro('finalizado')"
        class="rr-card rr-card-fin {{ $isActive ? 'rr-card-active' : ($hasFiltro ? 'rr-card-dim' : '') }}"
    >
        <div class="rr-label">Finalizados</div>
        <div class="rr-num">{{ $data['finalizados'] }}</div>
        <div class="rr-sub">{{ $data['pctResuelto'] }}% del total</div>
        <div class="rr-pct-bar">
            <div class="rr-pct-fill" style="width: {{ $data['pctResuelto'] }}%"></div>
        </div>
    </div>

</div>
@if($data['filtroActivo'])
    <div class="flex justify-center mt-2 mb-1">
        <button
            wire:click="seleccionarFiltro('all')"
            class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 underline"
        >
            × Quitar filtro
        </button>
    </div>
@endif
