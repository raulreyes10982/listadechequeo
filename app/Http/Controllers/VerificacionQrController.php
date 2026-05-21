<?php

namespace App\Http\Controllers;

use App\Models\Colaborador;
use App\Models\PuestoSeguridad;
use App\Models\RegistrarTurno;
use App\Models\VerificacionTurno;
use App\Support\ColaboradorUsuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VerificacionQrController extends Controller
{
    public function verificar(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                if (! Auth::check()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ No autenticado. Debe iniciar sesión.',
                    ], 401);
                }

                $codigoQrRaw = $request->input('codigo_qr') ?? $request->input('codigo');

                $validator = Validator::make(
                    [
                        'codigo_qr' => $codigoQrRaw,
                        'verificacion_id' => $request->input('verificacion_id'),
                    ],
                    [
                        'codigo_qr' => 'required|string',
                        'verificacion_id' => 'nullable|integer|exists:verificacion_turnos,id',
                    ]
                );

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ Formato inválido. Se espera JSON.',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                $qrData = json_decode($codigoQrRaw, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($qrData)) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ QR con formato JSON inválido.',
                    ], 422);
                }

                $validatorInterno = Validator::make($qrData, [
                    'codigo' => 'required|string|max:100',
                    'token' => 'required|string|size:64',
                ]);

                if ($validatorInterno->fails()) {
                    return response()->json([
                        'success' => false,
                        'mensaje' => '❌ Estructura del QR incorrecta. Faltan campos requeridos.',
                    ], 422);
                }

                $codigo = trim($qrData['codigo']);
                $token = trim($qrData['token']);

                $puesto = PuestoSeguridad::where('codigo', $codigo)->first();

                if (! $puesto) {
                    throw new \Exception('Puesto no encontrado en el sistema.');
                }

                if (! $puesto->validarTokenQr($token)) {
                    throw new \Exception('Token QR inválido o expirado.');
                }

                $colaborador = ColaboradorUsuario::actual();

                if (! $colaborador) {
                    throw new \Exception('Su usuario no está vinculado a un colaborador.');
                }

                $turno = $this->buscarTurnoActivo($puesto->id, $colaborador->id);

                if (! $turno) {
                    throw new \Exception('No tiene un turno activo asignado en este puesto.');
                }

                $verificacionExistente = $request->filled('verificacion_id')
                    ? VerificacionTurno::with('turno')->find($request->integer('verificacion_id'))
                    : null;

                if ($verificacionExistente) {
                    if ((int) $verificacionExistente->turno?->puesto_seguridad_id !== (int) $puesto->id) {
                        throw new \Exception('El QR no corresponde al puesto de esta verificación.');
                    }

                    if ((int) $verificacionExistente->turno?->colaborador_id !== (int) $colaborador->id) {
                        throw new \Exception('Esta verificación no pertenece a su turno.');
                    }

                    if ($verificacionExistente->estado === 'verificado') {
                        throw new \Exception('Esta verificación ya fue registrada.');
                    }

                    $verificacionExistente->marcarComoVerificado(
                        "Verificación {$verificacionExistente->tipo} vía QR - Puesto: {$puesto->codigo}"
                    );
                    $verificacion = $verificacionExistente->fresh();
                    $tipo = $verificacion->tipo;
                } else {
                    $tipo = $this->determinarTipoVerificacion($turno);

                    $verificacion = VerificacionTurno::create([
                        'registrar_turno_id' => $turno->id,
                        'tipo' => $tipo,
                        'hora_verificacion' => Carbon::now(),
                        'verificado_por' => Auth::id(),
                        'estado' => 'verificado',
                        'observacion' => "Verificación {$tipo} vía QR - Puesto: {$puesto->codigo}",
                    ]);
                }

                Log::info('Verificación QR exitosa', [
                    'verificacion_id' => $verificacion->id,
                    'puesto_id' => $puesto->id,
                    'turno_id' => $turno->id,
                    'colaborador_id' => $colaborador->id,
                    'usuario_id' => Auth::id(),
                    'tipo' => $tipo,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                return response()->json([
                    'success' => true,
                    'mensaje' => $this->getMensajePorTipo($tipo),
                    'data' => [
                        'verificacion_id' => $verificacion->id,
                        'tipo' => $tipo,
                        'hora' => $verificacion->hora_verificacion->format('H:i'),
                        'colaborador' => trim(($turno->colaborador->nombre ?? '').' '.($turno->colaborador->apellido ?? '')),
                        'puesto' => $puesto->puesto,
                        'codigo_puesto' => $puesto->codigo,
                        'fecha' => now()->format('d/m/Y'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Fallo en verificación QR: '.$e->getMessage(), [
                    'codigo_puesto' => $codigo ?? 'N/A',
                    'usuario_id' => Auth::id() ?? 'N/A',
                ]);

                return response()->json([
                    'success' => false,
                    'mensaje' => '❌ '.$e->getMessage(),
                ], 400);
            }
        });
    }

    private function buscarTurnoActivo(int $puestoId, int $colaboradorId): ?RegistrarTurno
    {
        $now = Carbon::now();

        return $this->turnoActivoQuery(
            RegistrarTurno::query()
                ->where('puesto_seguridad_id', $puestoId)
                ->where('colaborador_id', $colaboradorId),
            $now
        )
            ->with('colaborador')
            ->first();
    }

    private function turnoActivoQuery(Builder $query, Carbon $now): Builder
    {
        $currentDate = $now->toDateString();
        $previousDate = $now->copy()->subDay()->toDateString();
        $currentTime = $now->toTimeString();

        return $query->where(function ($query) use ($currentDate, $previousDate, $currentTime) {
            $query->where(function ($q) use ($currentDate, $currentTime) {
                $q->whereDate('fecha', $currentDate)
                    ->where(function ($inner) use ($currentTime) {
                        $inner->whereColumn('hora_inicio', '<=', 'hora_fin')
                            ->whereTime('hora_inicio', '<=', $currentTime)
                            ->whereTime('hora_fin', '>=', $currentTime);
                    })
                    ->orWhere(function ($inner) use ($currentTime) {
                        $inner->whereColumn('hora_inicio', '>', 'hora_fin')
                            ->whereTime('hora_inicio', '<=', $currentTime);
                    });
            })
            ->orWhere(function ($q) use ($previousDate, $currentTime) {
                $q->whereDate('fecha', $previousDate)
                    ->whereColumn('hora_inicio', '>', 'hora_fin')
                    ->whereTime('hora_fin', '>=', $currentTime);
            });
        });
    }

    private function determinarTipoVerificacion(RegistrarTurno $turno): string
    {
        $verificaciones = $turno->verificaciones()->where('estado', 'verificado')->get();

        if ($verificaciones->where('tipo', 'ingreso')->isEmpty()) {
            return 'ingreso';
        }

        $horaFin = Carbon::parse($turno->hora_fin);
        if ($verificaciones->where('tipo', 'salida')->isEmpty()
            && Carbon::now()->greaterThanOrEqualTo($horaFin)) {
            return 'salida';
        }

        return 'ronda';
    }

    private function getMensajePorTipo(string $tipo): string
    {
        return match ($tipo) {
            'ingreso' => '✅ Ingreso registrado correctamente',
            'salida' => '✅ Salida registrada correctamente',
            'ronda' => '✅ Ronda de verificación registrada',
            default => '✅ Verificación registrada',
        };
    }

    public function turnoActual(Request $request)
    {
        if (! Auth::check()) {
            return response()->json(['success' => false, 'estado' => 'no_autenticado'], 401);
        }

        $colaborador = ColaboradorUsuario::actual();

        if (! $colaborador) {
            return response()->json([
                'success' => true,
                'estado' => 'sin_turno',
                'mensaje' => 'Su usuario no está vinculado a un colaborador.',
            ]);
        }

        $now = Carbon::now();

        $turno = $this->turnoActivoQuery(
            RegistrarTurno::query()->where('colaborador_id', $colaborador->id),
            $now
        )
            ->with(['puestoSeguridad', 'verificaciones'])
            ->first();

        if (! $turno) {
            $turnoPendiente = RegistrarTurno::query()
                ->where('colaborador_id', $colaborador->id)
                ->whereDate('fecha', $now->toDateString())
                ->whereTime('hora_inicio', '>', $now->toTimeString())
                ->with('puestoSeguridad')
                ->orderBy('hora_inicio')
                ->first();

            if ($turnoPendiente) {
                return response()->json([
                    'success' => true,
                    'estado' => 'pendiente',
                    'turno_actual' => [
                        'puesto' => $turnoPendiente->puestoSeguridad->puesto,
                        'codigo_puesto' => $turnoPendiente->puestoSeguridad->codigo,
                        'hora_inicio' => Carbon::parse($turnoPendiente->hora_inicio)->format('H:i'),
                        'hora_fin' => Carbon::parse($turnoPendiente->hora_fin)->format('H:i'),
                        'verificaciones' => ['ingreso' => null, 'salida' => null],
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'estado' => 'sin_turno',
                'mensaje' => 'No tienes turnos asignados para hoy.',
            ]);
        }

        $verificaciones = $turno->verificaciones->where('estado', 'verificado');

        return response()->json([
            'success' => true,
            'estado' => 'activo',
            'turno_actual' => [
                'puesto' => $turno->puestoSeguridad->puesto,
                'codigo_puesto' => $turno->puestoSeguridad->codigo,
                'hora_inicio' => Carbon::parse($turno->hora_inicio)->format('H:i'),
                'hora_fin' => Carbon::parse($turno->hora_fin)->format('H:i'),
                'verificaciones' => [
                    'ingreso' => $verificaciones->firstWhere('tipo', 'ingreso')?->hora_verificacion?->format('H:i'),
                    'salida' => $verificaciones->firstWhere('tipo', 'salida')?->hora_verificacion?->format('H:i'),
                ],
            ],
        ]);
    }

    public function debugQR(Request $request)
    {
        if (! app()->environment('local')) {
            abort(404);
        }

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
            ],
        ]);
    }
}
