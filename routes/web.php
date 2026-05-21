<?php

use App\Http\Controllers\VerificacionQrController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/puestos/{puesto}/qr/descargar', function (\App\Models\PuestoSeguridad $puesto) {
        abort_unless(
            \App\Filament\Resources\PuestoSeguridadResource::puedeGestionarQr(),
            403
        );

        return \App\Filament\Resources\PuestoSeguridadResource::descargarQrImagen($puesto);
    })->name('puestos.qr.descargar');
    Route::post('/verificaciones/qr', [VerificacionQrController::class, 'verificar'])
        ->name('verificaciones.qr');

    Route::get('/turnos/actual', [VerificacionQrController::class, 'turnoActual'])
        ->name('turnos.actual');

    if (app()->environment('local')) {
        Route::post('/debug/qr', [VerificacionQrController::class, 'debugQR'])
            ->name('debug.qr')
            ->middleware('throttle:10,1');
    }
});
