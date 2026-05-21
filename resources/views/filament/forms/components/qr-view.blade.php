<div class="p-6 text-center space-y-4">
    <div class="flex justify-center bg-white rounded-lg p-4 mx-auto max-w-xs">
        {!! $qrCode !!}
    </div>

    <div class="mt-4 space-y-2 text-gray-700 dark:text-gray-300">
        <p class="text-lg font-semibold">
            Código: {{ $puesto->codigo }}
        </p>
        <p class="text-sm">
            Puesto: <span class="font-medium">{{ $puesto->puesto }}</span>
        </p>
        <p class="text-sm">
            Horario:
            <span class="font-medium">
                {{ $puesto->inicio_hora }} - {{ $puesto->fin_hora }}
            </span>
        </p>
        @if ($puesto->qr_expira)
            <p class="text-sm">
                Expira:
                <span class="font-medium {{ $puesto->qr_expira->isPast() ? 'text-danger-600' : 'text-success-600' }}">
                    {{ $puesto->qr_expira->format('d/m/Y H:i') }}
                </span>
            </p>
        @endif
    </div>

    @if (! empty($downloadUrl))
        <a
            href="{{ $downloadUrl }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-500"
        >
            Descargar QR
        </a>
    @endif
</div>
