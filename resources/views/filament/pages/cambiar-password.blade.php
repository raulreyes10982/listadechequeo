<x-filament-panels::page>

    <div class="max-w-lg mx-auto mt-8">

        {{-- Alerta de contraseña temporal --}}
        <div class="mb-6 flex items-start gap-3 rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-700 dark:bg-warning-900/20 p-4">
            <x-filament::icon
                icon="heroicon-m-exclamation-triangle"
                class="h-5 w-5 text-warning-500 flex-shrink-0 mt-0.5"
            />
            <div>
                <p class="text-sm font-semibold text-warning-800 dark:text-warning-200">
                    Contraseña temporal detectada
                </p>
                <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                    Tu cuenta fue creada con una contraseña temporal. Por tu seguridad,
                    debes establecer una contraseña personal antes de continuar.
                </p>
            </div>
        </div>

        {{-- Formulario --}}
        <x-filament::section>
            <form wire:submit="guardar">

                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        size="lg"
                        class="w-full"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Guardar nueva contraseña</span>
                        <span wire:loading>Guardando...</span>
                    </x-filament::button>
                </div>

            </form>
        </x-filament::section>

    </div>

</x-filament-panels::page>
