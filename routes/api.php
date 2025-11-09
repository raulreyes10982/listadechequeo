<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionQrController;


Route::middleware('auth:sanctum')->post('/verificar-qr', [VerificacionQrController::class, 'verificar']);

