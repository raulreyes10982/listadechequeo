<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificacionQrController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes para Sistema de Seguridad
|--------------------------------------------------------------------------
*/

// 🔓 RUTA PÚBLICA PARA LOGIN API
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);
    
    $user = \App\Models\User::where('email', $request->email)->first();
    
    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false, 
            'mensaje' => 'Credenciales incorrectas'
        ], 401);
    }
    
    // Verificar que tenga rol válido
    if (!$user->hasAnyRole(['guardia', 'supervisor', 'administrador', 'super_admin'])) {
        return response()->json([
            'success' => false,
            'mensaje' => 'Usuario no tiene permisos para acceder al sistema'
        ], 403);
    }
    
    $token = $user->crearTokenAppMovil();
    
    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]
    ]);
});

// 🔐 RUTAS PROTEGIDAS POR SANCTUM
Route::middleware(['auth:sanctum'])->group(function () {
    
    // 👤 INFORMACIÓN DEL USUARIO
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name')
            ]
        ]);
    });
    
    // 📍 VERIFICACIÓN QR (app móvil con token Sanctum)
    Route::post('/verificar-qr', [VerificacionQrController::class, 'verificar'])
        ->name('api.verificar.qr');

    Route::get('/turno-actual', [VerificacionQrController::class, 'turnoActual'])
        ->name('api.turno.actual');
    
    // 🚪 LOGOUT
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'mensaje' => 'Sesión cerrada correctamente'
        ]);
    });
});