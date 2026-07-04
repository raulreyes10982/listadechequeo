<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VerificacionTurno extends Model
{
    // ✅ CORRECCIÓN: Eliminado Notifiable y HasRoles — estos traits son solo para User
    use HasFactory;

    protected $table = 'verificacion_turnos';

    protected $fillable = [
        'registrar_turno_id',
        'tipo',
        'hora_verificacion',
        'observacion',
        'verificado_por',
        'estado',
    ];

    protected $casts = [
        'hora_verificacion' => 'datetime',
    ];

    /* ============================================
       RELACIONES
    ============================================ */

    public function turno()
    {
        return $this->belongsTo(RegistrarTurno::class, 'registrar_turno_id');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    /* ============================================
       ACCESORES CORREGIDOS
    ============================================ */

    /**
     * ✅ CORRECCIÓN: antes llamaba a $this->puestoSeguridad que no existe.
     * Ahora accede correctamente a través de la relación turno → puestoSeguridad.
     */
    public function getPuestoAttribute()
    {
        return $this->turno?->puestoSeguridad?->codigo ?? null;
    }

    /* ============================================
       BOOT Y EVENTOS
    ============================================ */

    protected static function booted()
    {
        static::creating(function ($verificacion) {
            // Asignar verificador y hora al crear con estado verificado
            if (Auth::check() && $verificacion->estado === 'verificado') {
                $verificacion->verificado_por    = Auth::id();
                $verificacion->hora_verificacion = now();
            }
        });

        // ✅ CORRECCIÓN: también asignar verificador al actualizar a estado verificado
        static::updating(function ($verificacion) {
            if ($verificacion->isDirty('estado') && $verificacion->estado === 'verificado') {
                $verificacion->verificado_por    = Auth::id();
                $verificacion->hora_verificacion = now();
            }
        });

        static::created(function ($verificacion) {
            Log::info('Verificación creada', [
                'id'       => $verificacion->id,
                'tipo'     => $verificacion->tipo,
                'turno_id' => $verificacion->registrar_turno_id,
                'usuario'  => Auth::id() ?? 'sistema',
            ]);
        });
    }

    /* ============================================
       SCOPES
    ============================================ */

    public function scopeDelDia($query)
    {
        return $query->whereDate('hora_verificacion', Carbon::today());
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeVerificadas($query)
    {
        return $query->where('estado', 'verificado');
    }

    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('verificado_por', $usuarioId);
    }

    public function scopeDelPuesto($query, $puestoId)
    {
        return $query->whereHas('turno', function ($q) use ($puestoId) {
            $q->where('puesto_seguridad_id', $puestoId);
        });
    }

    /* ============================================
       MÉTODOS DE INSTANCIA
    ============================================ */

    public function marcarComoVerificado($observacion = null)
    {
        $this->update([
            'estado'            => 'verificado',
            'hora_verificacion' => now(),
            'verificado_por'    => Auth::id(),
            'observacion'       => $observacion ?? $this->observacion,
        ]);

        Log::info('Verificación marcada como verificada', [
            'verificacion_id' => $this->id,
            'usuario'         => Auth::id(),
        ]);

        return $this;
    }

    public function marcarComoPendiente()
    {
        $this->update([
            'estado'            => 'pendiente',
            'hora_verificacion' => null,
            'verificado_por'    => null,
        ]);

        return $this;
    }

    public function estaExpirada()
    {
        if ($this->estado === 'verificado') {
            return false;
        }

        return ($this->created_at ?? now())->diffInHours(now()) > 24;
    }

    public function puedeEditar()
    {
        return $this->estado === 'pendiente' && ! $this->estaExpirada();
    }

    /* ============================================
       ATRIBUTOS FORMATEADOS
    ============================================ */

    public function getHoraFormateadaAttribute()
    {
        return $this->hora_verificacion
            ? $this->hora_verificacion->format('H:i')
            : '--:--';
    }

    public function getFechaFormateadaAttribute()
    {
        return $this->hora_verificacion
            ? $this->hora_verificacion->format('d/m/Y')
            : '--/--/----';
    }

    public function getColorTipoAttribute()
    {
        return match ($this->tipo) {
            'ingreso'   => 'primary',
            'salida'    => 'success',
            'ronda'     => 'warning',
            'reemplazo' => 'info',
            default     => 'gray',
        };
    }

    public function getColorEstadoAttribute()
    {
        return match ($this->estado) {
            'verificado' => 'success',
            'pendiente'  => 'warning',
            'cerrado'    => 'danger',
            default      => 'gray',
        };
    }

    public function getIconoTipoAttribute()
    {
        return match ($this->tipo) {
            'ingreso'   => 'heroicon-o-arrow-right-circle',
            'salida'    => 'heroicon-o-arrow-left-circle',
            'ronda'     => 'heroicon-o-shield-check',
            'reemplazo' => 'heroicon-o-user-plus',
            default     => 'heroicon-o-question-mark-circle',
        };
    }

    /* ============================================
       MÉTODO ESTÁTICO — REGISTRO DESDE QR
    ============================================ */

    /**
     * Registrar verificación desde QR escaneado.
     *
     * ✅ CORRECCIÓN: antes usaba $usuario->colaborador_id (columna que no existe
     * en la tabla users). Ahora usa $usuario->resolverColaborador() definido
     * en el modelo User, que busca por user_id, correo_corporativo o correo_personal.
     */
    public static function registrarDesdeQR(string $codigoQr, ?int $verificacionId = null)
    {
        try {
            $usuario = Auth::user();
            $ahora   = Carbon::now();

            if (! $usuario) {
                return ['error' => 'Usuario no autenticado.'];
            }

            // Buscar puesto por código QR
            $puesto = PuestoSeguridad::where('codigo', $codigoQr)->first();

            if (! $puesto) {
                return ['error' => 'Código QR no pertenece a un puesto válido.'];
            }

            // ✅ CORRECCIÓN: usar resolverColaborador() en lugar de $usuario->colaborador_id
            $colaborador = $usuario->resolverColaborador();

            if (! $colaborador) {
                return ['error' => 'No se encontró un colaborador asociado a tu usuario.'];
            }

            // Buscar turno activo del colaborador en este puesto
            $turno = RegistrarTurno::where('colaborador_id', $colaborador->id)
                ->where('puesto_seguridad_id', $puesto->id)
                ->whereDate('fecha', $ahora->toDateString())
                ->whereTime('hora_inicio', '<=', $ahora->toTimeString())
                ->whereTime('hora_fin', '>=', $ahora->toTimeString())
                ->first();

            if (! $turno) {
                return ['error' => 'No hay turno activo para este puesto en este momento.'];
            }

            // Si viene un verificacionId específico (desde el modal de Filament), usarlo
            if ($verificacionId) {
                $verificacion = self::where('id', $verificacionId)
                    ->where('registrar_turno_id', $turno->id)
                    ->where('estado', 'pendiente')
                    ->first();

                if (! $verificacion) {
                    return ['error' => 'La verificación no existe o ya fue procesada.'];
                }

                $tipo = $verificacion->tipo;
            } else {
                // Determinar tipo automáticamente por la última verificación
                $ultima = self::where('registrar_turno_id', $turno->id)
                    ->where('estado', 'verificado')
                    ->orderBy('hora_verificacion', 'desc')
                    ->first();

                $tipo = 'ingreso';

                if ($ultima) {
                    $horaFin = Carbon::parse($turno->hora_fin);

                    if ($ahora->greaterThanOrEqualTo($horaFin) && $ultima->tipo !== 'salida') {
                        $tipo = 'salida';
                    } elseif ($ultima->tipo === 'ingreso') {
                        $tipo = 'ronda';
                    }
                }

                $verificacion = self::where('registrar_turno_id', $turno->id)
                    ->where('tipo', $tipo)
                    ->where('estado', 'pendiente')
                    ->first();

                if (! $verificacion) {
                    return ['error' => "No hay verificación pendiente de tipo '{$tipo}' para este turno."];
                }
            }

            // Validar token QR del puesto
            if (! $puesto->validarTokenQr($codigoQr) && $codigoQr !== $puesto->codigo) {
                // Si el QR contiene JSON, extraer el token
                $datos = json_decode($codigoQr, true);
                if ($datos && isset($datos['token']) && ! $puesto->validarTokenQr($datos['token'])) {
                    return ['error' => 'Token QR inválido o expirado.'];
                }
            }

            // Marcar como verificada
            $verificacion->update([
                'estado'            => 'verificado',
                'hora_verificacion' => $ahora,
                'verificado_por'    => $usuario->id,
                'observacion'       => "Verificación {$tipo} vía QR",
            ]);

            Log::info('Verificación QR registrada', [
                'verificacion_id' => $verificacion->id,
                'tipo'            => $tipo,
                'puesto'          => $puesto->codigo,
                'usuario'         => $usuario->id,
            ]);

            return [
                'success'      => true,
                'tipo'         => $tipo,
                'mensaje'      => match ($tipo) {
                    'ingreso' => '✅ Ingreso registrado correctamente',
                    'salida'  => '✅ Salida registrada correctamente',
                    'ronda'   => '✅ Ronda de verificación registrada',
                    default   => '✅ Verificación registrada',
                },
                'verificacion' => $verificacion,
                'data'         => [
                    'id'           => $verificacion->id,
                    'hora'         => $verificacion->hora_formateada,
                    'puesto'       => $puesto->puesto,
                    'codigo_puesto' => $puesto->codigo,
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Error en registrarDesdeQR: ' . $e->getMessage());

            return [
                'error' => 'Error interno al procesar el QR.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ];
        }
    }
}
