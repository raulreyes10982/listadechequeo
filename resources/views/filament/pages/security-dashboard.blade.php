<x-filament-panels::page>

    {{-- Reloj en tiempo real --}}
    <div
        x-data="{ hora: '' }"
        x-init="
            hora = new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' — ' + new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            setInterval(() => {
                hora = new Date().toLocaleDateString('es-CO', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' — ' + new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }, 1000);
        "
        class="flex items-center justify-between mb-4"
    >
        <p class="text-sm text-gray-500 dark:text-gray-400 capitalize" x-text="hora"></p>
        <span class="inline-flex items-center gap-1.5 text-xs text-green-600 dark:text-green-400 font-medium">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            En vivo · actualización automática
        </span>
    </div>

    {{-- Pestañas de navegación --}}
    <style>
        .scd-tabs {
            display: inline-flex;
            gap: 4px;
            background: rgb(244 244 245);
            border-radius: 14px;
            padding: 5px;
        }
        .dark .scd-tabs { background: rgb(39 39 42); }

        .scd-tab {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            line-height: 1;
            border: none;
            background: transparent;
            color: rgb(113 113 122);
            cursor: pointer;
            transition: all .18s ease;
            white-space: nowrap;
        }
        .dark .scd-tab { color: rgb(161 161 170); }

        .scd-tab:hover:not(.scd-tab-active) {
            color: rgb(63 63 70);
            background: rgba(255,255,255,.5);
        }
        .dark .scd-tab:hover:not(.scd-tab-active) {
            color: rgb(212 212 216);
            background: rgba(255,255,255,.04);
        }

        .scd-tab-active {
            background: #fff;
            color: rgb(24 24 27);
            box-shadow: 0 1px 2px rgba(0,0,0,.05), 0 1px 6px rgba(0,0,0,.04);
        }
        .dark .scd-tab-active {
            background: rgb(63 63 70);
            color: #fff;
        }

        .scd-tab-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            display: inline-flex;
        }
        .scd-tab-icon svg { width: 100%; height: 100%; }

        /* Color de acento por pestaña cuando está activa */
        .scd-tab-active[data-tab="seguridad"] .scd-tab-icon { color: rgb(34 197 94); }
        .scd-tab-active[data-tab="reportes"]  .scd-tab-icon { color: rgb(59 130 246); }
        .scd-tab-active[data-tab="tecnico"]   .scd-tab-icon { color: rgb(249 115 22); }
    </style>

    <div class="flex justify-center mb-6">
        <div class="scd-tabs">
            @foreach ($this->getTabs() as $key => $tab)
                <button
                    wire:click="setTab('{{ $key }}')"
                    data-tab="{{ $key }}"
                    class="scd-tab {{ $activeTab === $key ? 'scd-tab-active' : '' }}"
                >
                    <span class="scd-tab-icon">
                        <x-filament::icon :icon="$tab['icon']" />
                    </span>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Widgets de la pestaña activa --}}
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />

</x-filament-panels::page>
