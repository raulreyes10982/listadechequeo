<div class="p-6 text-center space-y-4">
    {{-- QR Code --}}
    <div class="flex justify-center">
        {!! $qrCode !!}
    </div>

    {{-- Información del puesto --}}
    <div class="mt-4 text-gray-300 space-y-2">
        <p class="text-lg font-semibold text-white">
            Código: {{ $puesto->codigo }}
        </p>
        <p class="text-sm text-gray-400">
            Puesto: <span class="font-medium text-gray-200">{{ $puesto->puesto }}</span>
        </p>
        <p class="text-sm text-gray-400">
            Horario: 
            <span class="font-medium text-gray-200">
                {{ $puesto->inicio_hora }} - {{ $puesto->fin_hora }}
            </span>
        </p>
        <p class="text-sm text-gray-400">
            Expira: 
            <span class="font-medium {{ $puesto->qr_expira->isPast() ? 'text-red-500' : 'text-green-400' }}">
                {{ $puesto->qr_expira->format('d/m/Y H:i') }}
            </span>
        </p>
        <p class="text-xs text-gray-500 break-all">
            Token: {{ $puesto->qr_token }}
        </p>
    </div>
</div>
