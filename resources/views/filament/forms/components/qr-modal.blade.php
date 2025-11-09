<div class="p-4 text-center">
    @if(isset($qrCode))
        <div class="mb-4">
            {!! $qrCode !!}
        </div>
        <div class="space-y-2">
            <p class="text-sm font-medium text-gray-900">Código: {{ $codigo }}</p>
            <p class="text-sm text-gray-700">Puesto: {{ $puesto }}</p>
            <p class="text-sm text-gray-600">Horario: {{ $horario }}</p>
            <p class="text-sm text-gray-600">Expira: {{ $expira }}</p>
            <p class="text-xs text-gray-500 break-all">Token: {{ $token }}</p>
        </div>
    @else
        <p class="text-gray-500">No se pudo generar el código QR</p>
    @endif
</div>