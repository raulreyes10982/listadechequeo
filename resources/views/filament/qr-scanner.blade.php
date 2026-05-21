{{-- Escáner QR: cámara web en PC, cámara trasera en móvil --}}
@php
    $verifyUrl = $verifyUrl ?? route('verificaciones.qr');
    $verificacionId = $verificacionId ?? null;
    $readerId = 'qr-reader-' . ($verificacionId ?? uniqid());
@endphp

<div
    wire:ignore
    x-data="qrScanner({
        verifyUrl: @js($verifyUrl),
        verificacionId: @js($verificacionId),
        readerId: @js($readerId),
    })"
    class="text-center"
>
    <div id="{{ $readerId }}" class="w-full min-h-[280px] rounded-lg overflow-hidden bg-gray-900"></div>

    <p x-show="!mensajeError" class="text-gray-500 text-sm mt-3" x-text="mensajeCamara"></p>
    <p x-show="mensajeError" class="text-red-600 text-sm mt-3" x-text="mensajeError"></p>

    <p x-show="procesando" class="text-blue-600 text-sm mt-2">Verificando código...</p>
</div>
