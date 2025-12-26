<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificacionTurno;
use Illuminate\Support\Facades\Auth;

class VerificacionQrController extends Controller
{
    public function verificar(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'error' => 'Usuario no autenticado.'
                ], 401);
            }

            $codigoQr = $request->input('codigo');
            if (!$codigoQr) {
                return response()->json([
                    'error' => 'Debe enviar el código QR.'
                ], 400);
            }

            $resultado = VerificacionTurno::registrarDesdeQR($codigoQr);

            if (isset($resultado['error'])) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $resultado['error']
                ], 422);
            }

            return response()->json([
                'success' => true,
                'tipo' => $resultado['tipo'],
                'mensaje' => $resultado['mensaje'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la verificación: ' . $e->getMessage(),
            ], 500);
        }
    }
}
