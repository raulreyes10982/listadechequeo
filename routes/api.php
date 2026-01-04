<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionQrController;

Route::post('/verificar-qr', [VerificacionQrController::class, 'verificar'])
    ->middleware('auth:sanctum');