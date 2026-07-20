@php $d = $this->getData(); $f = $d['filtroActivo']; @endphp

<div>
<style>
  .rri { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:12px; }
  @media(max-width:900px){ .rri { grid-template-columns:repeat(3,1fr); } }
  @media(max-width:560px){ .rri { grid-template-columns:repeat(2,1fr); } }

  .rri-c {
    background: white;
    border-radius: 12px;
    padding: 16px 18px;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    position: relative;
  }
  .dark .rri-c { background: #1f2937; border-color: #374151; }
  .rri-c:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
  .rri-dim { opacity: .35; filter: grayscale(.4); }
  .rri-on  { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.12); }
  .rri-on::after { content:'✓'; position:absolute; top:8px; right:10px; font-size:12px; opacity:.5; }

  .rri-label { font-size:11px; color:#6b7280; margin-bottom:6px; font-weight:500; }
  .dark .rri-label { color:#9ca3af; }
  .rri-num { font-size:30px; font-weight:700; line-height:1; margin-bottom:5px; }
  .rri-sub  { font-size:11px; color:#9ca3af; }
  .rri-bar  { height:4px; background:#f3f4f6; border-radius:2px; margin-top:8px; overflow:hidden; }
  .dark .rri-bar { background:#374151; }
  .rri-fill { height:100%; border-radius:2px; transition:width .4s; }

  .rri-quitar { display:flex; justify-content:center; margin-top:8px; }
  .rri-quitar button {
    font-size:11px; color:#9ca3af; background:none; border:none; cursor:pointer;
    text-decoration:underline; padding:2px 8px;
  }
  .rri-quitar button:hover { color:#6b7280; }
</style>

<div class="rri">

  {{-- TOTAL --}}
  <div wire:click="seleccionarFiltro('all')"
       style="border-color:#6366f1"
       class="rri-c {{ $f === null ? '' : 'rri-dim' }}">
    <div class="rri-label">Total reportes</div>
    <div class="rri-num" style="color:#6366f1">{{ $d['total'] }}</div>
    <div class="rri-sub">Hoy: {{ $d['hoyCount'] }} nuevo(s)</div>
  </div>

  {{-- CRÍTICOS --}}
  <div wire:click="seleccionarFiltro('critico')"
       style="border-color:#ef4444"
       class="rri-c {{ $f === 'critico' ? 'rri-on' : ($f !== null ? 'rri-dim' : '') }}">
    <div class="rri-label">Críticos</div>
    <div class="rri-num" style="color:#ef4444">{{ $d['criticos'] }}</div>
    <div class="rri-sub">Prioridad alta sin cerrar</div>
  </div>

  {{-- PENDIENTES --}}
  <div wire:click="seleccionarFiltro('pendiente')"
       style="border-color:#f59e0b"
       class="rri-c {{ $f === 'pendiente' ? 'rri-on' : ($f !== null ? 'rri-dim' : '') }}">
    <div class="rri-label">Pendientes</div>
    <div class="rri-num" style="color:#f59e0b">{{ $d['pendientes'] }}</div>
    <div class="rri-sub">Sin atender</div>
  </div>

  {{-- EN PROCESO --}}
  <div wire:click="seleccionarFiltro('en_proceso')"
       style="border-color:#3b82f6"
       class="rri-c {{ $f === 'en_proceso' ? 'rri-on' : ($f !== null ? 'rri-dim' : '') }}">
    <div class="rri-label">En proceso</div>
    <div class="rri-num" style="color:#3b82f6">{{ $d['enProceso'] }}</div>
    <div class="rri-sub">Siendo atendidos</div>
  </div>

  {{-- FINALIZADOS --}}
  <div wire:click="seleccionarFiltro('finalizado')"
       style="border-color:#22c55e"
       class="rri-c {{ $f === 'finalizado' ? 'rri-on' : ($f !== null ? 'rri-dim' : '') }}">
    <div class="rri-label">Finalizados</div>
    <div class="rri-num" style="color:#22c55e">{{ $d['finalizados'] }}</div>
    <div class="rri-sub">{{ $d['pctResuelto'] }}% del total</div>
    <div class="rri-bar">
      <div class="rri-fill" style="width:{{ $d['pctResuelto'] }}%; background:#22c55e"></div>
    </div>
  </div>

</div>

@if($f && $f !== 'all')
  <div class="rri-quitar">
    <button wire:click="seleccionarFiltro('all')">× Quitar filtro</button>
  </div>
@endif

</div>
