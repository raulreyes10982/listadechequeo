<?php

namespace App\Http\Controllers;

use App\Models\PuestoSeguridad;
use App\Models\RegistrarTurno;
use App\Models\VerificacionTurno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;   
use Carbon\Carbon;

class VerificacionQrController extends Controller
{

    public function verificar(Request $request)
    {
        // 🔄 TRANSACCIÓN PARA CONSISTENCIA
        return DB::transaction(function () use ($request) {
            try {
                // 🔐 1. VERIFICAR AUTENTICACIÓN
                if (!Auth::check()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ No autenticado. Debe iniciar sesión.'
                    ], 401);
                }

                // ✅ 2. VALIDACIÓN ESTRICTA DE ENTRADA
                $validator = Validator::make($request->all(), [
                    'codigo_qr' => 'required|json',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ Formato inválido. Se espera JSON.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Decodificar el QR
                $qrData = json_decode($request->input('codigo_qr'), true);
                
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($qrData)) {
                    return response()->json([
                        'success' => false, 
                        'mensaje' => '❌ QR con formato JSON inválido.'
                    ], 422);
                }

                // Validar estructura interna del QR
                $validatorInterno = Validator::make($qrData, [
                    'codigo' => 'required|string|max:100',
                    'token'  => 'required|string|size:64', // SHA256 = 64 chars
                ]);

                if ($validatorInterno->fails()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ Estructura del QR incorrecta. Faltan campos requeridos.'
                    ], 422);
                }

                $codigo = trim($qrData['codigo']);
                $token = trim($qrData['token']);
                
                // 3. BUSCAR PUESTO
                $puesto = PuestoSeguridad::where('codigo', $codigo)->first();
                
                if (!$puesto) {
                    throw new \Exception('Puesto no encontrado en el sistema.');
                }
                
                // 4. VALIDAR TOKEN QR DE MANERA SEGURA
                if (!$puesto->validarTokenQr($token)) {
                    throw new \Exception('Token QR inválido o expirado.');
                }
                
                // 5. BUSCAR TURNO ACTIVO PARA ESTE PUESTO
                $turno = $this->buscarTurnoActivo($puesto->id);
                
                if (!$turno) {
                    throw new \Exception('No hay turno activo asignado para este puesto.');
                }
                
                // 6. DETERMINAR TIPO DE VERIFICACIÓN
                $tipo = $this->determinarTipoVerificacion($turno);
                
                // 7. CREAR VERIFICACIÓN
                $verificacion = VerificacionTurno::create([
                    'registrar_turno_id' => $turno->id,
                    'tipo' => $tipo,
                    'hora_verificacion' => Carbon::now(),
                    'verificado_por' => Auth::id(),
                    'estado' => 'verificado',
                    'observacion' => "Verificación {$tipo} vía QR - Puesto: {$puesto->codigo}"
                ]);
                
                // 8. LOG PARA AUDITORÍA
                Log::info('Verificación QR exitosa', [
                    'verificacion_id' => $verificacion->id,
                    'puesto_id' => $puesto->id,
                    'turno_id' => $turno->id,
                    'usuario_id' => Auth::id(),
                    'tipo' => $tipo,
                    'timestamp' => now()->toDateTimeString()
                ]);
                
                // ✅ ÉXITO - La transacción se confirma automáticamente
                return response()->json([
                    'success' => true,
                    'mensaje' => $this->getMensajePorTipo($tipo),
                    'data' => [
                        'verificacion_id' => $verificacion->id,
                        'tipo' => $tipo,
                        'hora' => $verificacion->hora_verificacion->format('H:i'),
                        'colaborador' => $turno->colaborador->nombre ?? 'N/A',
                        'puesto' => $puesto->puesto,
                        'codigo_puesto' => $puesto->codigo,
                        'fecha' => now()->format('d/m/Y')
                    ]
                ]);
                
            } catch (\Exception $e) {
                // ❌ ERROR - La transacción se revierte automáticamente
                Log::error('Fallo en verificación QR (transacción revertida): ' . $e->getMessage(), [
                    'codigo_puesto' => $codigo ?? 'N/A',
                    'usuario_id' => Auth::id() ?? 'N/A',
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'mensaje' => '❌ ' . $e->getMessage()
                ], 400);
            }
        });
    }
    
    private function buscarTurnoActivo($puestoId)
    {
        $now = Carbon::now();
        
        return RegistrarTurno::where('puesto_seguridad_id', $puestoId)
            ->whereDate('fecha', $now->toDateString())
            ->where(function($query) use ($now) {
                // Turno normal (mismo día)
                $query->where(function($q) use ($now) {
                    $q->whereTime('hora_inicio', '<=', $now->toTimeString())
                    ->whereTime('hora_fin', '>=', $now->toTimeString());
                })
                // Turno nocturno (cruza medianoche)
                ->orWhere(function($q) use ($now) {
                    $q->whereTime('hora_inicio', '>=', '18:00:00')
                    ->whereDate('fecha', $now->copy()->subDay()->toDateString())
                    ->whereTime('hora_fin', '<=', '06:00:00');
                });
            })
            ->with('colaborador')
            ->first();
    }
    
    private function determinarTipoVerificacion($turno): string
    {
        $verificaciones = $turno->verificaciones()->where('estado', 'verificado')->get();
        
        // Primera verificación del día = INGRESO
        if ($verificaciones->where('tipo', 'ingreso')->isEmpty()) {
            return 'ingreso';
        }
        
        // Si ya pasó la hora de fin = SALIDA
        $horaFin = Carbon::parse($turno->hora_fin);
        if ($verificaciones->where('tipo', 'salida')->isEmpty() 
            && Carbon::now()->greaterThanOrEqualTo($horaFin)) {
            return 'salida';
        }
        
        // Por defecto = RONDA
        return 'ronda';
    }
    
    private function getMensajePorTipo(string $tipo): string
    {
        return match($tipo) {
            'ingreso' => '✅ Ingreso registrado correctamente',
            'salida'  => '✅ Salida registrada correctamente',
            'ronda'   => '✅ Ronda de verificación registrada',
            default   => '✅ Verificación registrada'
        };
    }
    
    public function turnoActual(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'estado' => 'no_autenticado'], 401);
        }
        
        $now = Carbon::now();
        $usuario = Auth::user();
        
        // Suponiendo que User tiene relación con Colaborador
        // $colaboradorId = $usuario->colaborador_id;
        
        // Para prueba, buscar cualquier turno activo
        $turno = RegistrarTurno::whereDate('fecha', $now->toDateString())
            ->whereTime('hora_inicio', '<=', $now->toTimeString())
            ->whereTime('hora_fin', '>=', $now->toTimeString())
            ->with(['puestoSeguridad', 'verificaciones'])
            ->first();
        
        if (!$turno) {
            return response()->json([
                'success' => true,
                'estado' => 'sin_turno',
                'mensaje' => 'No tienes turnos activos en este momento.'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'estado' => 'activo',
            'turno_actual' => [
                'puesto' => $turno->puestoSeguridad->puesto,
                'codigo_puesto' => $turno->puestoSeguridad->codigo,
                'hora_inicio' => Carbon::parse($turno->hora_inicio)->format('H:i'),
                'hora_fin' => Carbon::parse($turno->hora_fin)->format('H:i'),
                'verificaciones_hoy' => $turno->verificaciones->count()
            ]
        ]);
    }
    
    public function debugQR(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_qr' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $data = json_decode($request->input('codigo_qr'), true);
        
        return response()->json([
            'success' => true,
            'debug_info' => [
                'raw_input' => $request->input('codigo_qr'),
                'parsed_json' => $data,
                'is_valid_json' => json_last_error() === JSON_ERROR_NONE,
                'json_error' => json_last_error_msg(),
                'has_codigo_field' => isset($data['codigo']),
                'has_token_field' => isset($data['token']),
                'codigo_length' => isset($data['codigo']) ? strlen($data['codigo']) : 0,
                'token_length' => isset($data['token']) ? strlen($data['token']) : 0,
            ]
        ]);
    }
}