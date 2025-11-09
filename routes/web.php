<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionQrController;



Route::get('/', function () {return view('welcome');});
Route::middleware('auth:sanctum')->post('/verificar-qr', [VerificacionQrController::class, 'verificar']);

