<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionQrController;

// 🔐 RUTAS PROTEGIDAS POR AUTENTICACIÓN
Route::middleware(['auth:sanctum'])->group(function () {
    
    // VERIFICACIÓN QR (solo guardias, supervisores y admin)
    Route::post('/verificaciones/qr', [VerificacionQrController::class, 'verificar'])
        ->name('api.verificar.qr');
         // ->middleware('permission:scan_qr'); // Descomentar cuando tengas Spatie
    
    // TURNO ACTUAL DEL USUARIO
    Route::get('/turnos/actual', [VerificacionQrController::class, 'turnoActual'])
        ->name('api.turno.actual');
    
    // DEBUG QR (limitado a 10 intentos por minuto)
    Route::post('/debug/qr', [VerificacionQrController::class, 'debugQR'])
        ->name('api.debug.qr')
        ->middleware('throttle:10,1'); // 10 intentos por minuto
});