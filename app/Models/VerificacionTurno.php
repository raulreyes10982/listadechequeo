<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VerificacionTurno extends Model
{
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
       RELACIONES CORREGIDAS
    ============================================ */
    
    /**
     * Relación con el turno
     */
    public function turno() 
    {
        return $this->belongsTo(RegistrarTurno::class, 'registrar_turno_id');
    }

    /**
     * Relación con el usuario verificador
     */
    public function verificador() 
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    /**
     * 🔧 CORRECCIÓN: Acceso al puesto a través del turno (MÉTODO NUEVO)
     */
    public function getPuestoAttribute()
    {
        return $this->puestoSeguridad->codigo - $this->turno->puestoSeguridad  ?? null;
    }

    /**
     * 🔧 CORRECCIÓN: Relación directa para queries (OPCIONAL)
     */
    public function puesto()
    {
        return $this->hasOneThrough(
            PuestoSeguridad::class,
            RegistrarTurno::class,
            'id', // Primary key en RegistrarTurno
            'id', // Primary key en PuestoSeguridad  
            'registrar_turno_id', // Foreign key en VerificacionTurno
            'puesto_seguridad_id' // Foreign key en RegistrarTurno
        );
    }

    /* ============================================
       BOOT Y EVENTOS
    ============================================ */
    
    /**
     * Boot: asignar automáticamente verificador y hora
     */
    protected static function booted() 
    {
        static::creating(function ($verificacion) {
            if (Auth::check() && $verificacion->estado === 'verificado') {
                $verificacion->verificado_por = Auth::id();
                $verificacion->hora_verificacion = now();
            }
        });

        static::created(function ($verificacion) {
            Log::info('Verificación creada', [
                'id' => $verificacion->id,
                'tipo' => $verificacion->tipo,
                'turno_id' => $verificacion->registrar_turno_id,
                'usuario' => Auth::id() ?? 'sistema'
            ]);
        });
    }

    /* ============================================
       SCOPES PARA CONSULTAS FRECUENTES
    ============================================ */
    
    /**
     * Scope: Verificaciones del día actual
     */
    public function scopeDelDia($query)
    {
        return $query->whereDate('hora_verificacion', Carbon::today());
    }

    /**
     * Scope: Verificaciones por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Verificaciones verificadas
     */
    public function scopeVerificadas($query)
    {
        return $query->where('estado', 'verificado');
    }

    /**
     * Scope: Verificaciones de un usuario específico
     */
    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('verificado_por', $usuarioId);
    }

    /**
     * Scope: Verificaciones de un puesto específico
     */
    public function scopeDelPuesto($query, $puestoId)
    {
        return $query->whereHas('turno', function($q) use ($puestoId) {
            $q->where('puesto_seguridad_id', $puestoId);
        });
    }

    /* ============================================
       MÉTODOS DE INSTANCIA
    ============================================ */
    
    /**
     * Marcar como verificado
     */
    public function marcarComoVerificado($observacion = null)
    {
        $this->update([
            'estado' => 'verificado',
            'hora_verificacion' => now(),
            'verificado_por' => Auth::id(),
            'observacion' => $observacion ?? $this->observacion
        ]);

        Log::info('Verificación marcada como verificada', [
            'verificacion_id' => $this->id,
            'usuario' => Auth::id()
        ]);

        return $this;
    }

    /**
     * Marcar como pendiente
     */
    public function marcarComoPendiente()
    {
        $this->update([
            'estado' => 'pendiente',
            'hora_verificacion' => null,
            'verificado_por' => null
        ]);

        return $this;
    }

    /**
     * Obtener hora formateada
     */
    public function getHoraFormateadaAttribute()
    {
        return $this->hora_verificacion 
            ? $this->hora_verificacion->format('H:i') 
            : '--:--';
    }

    /**
     * Obtener fecha formateada
     */
    public function getFechaFormateadaAttribute()
    {
        return $this->hora_verificacion 
            ? $this->hora_verificacion->format('d/m/Y') 
            : '--/--/----';
    }

    /**
     * Verificar si está expirada (más de 24 horas sin verificar)
     */
    public function estaExpirada()
    {
        if ($this->estado === 'verificado') {
            return false;
        }

        $creacion = $this->created_at ?? now();
        return $creacion->diffInHours(now()) > 24;
    }

    /* ============================================
       MÉTODO ESTÁTICO ACTUALIZADO (MÁS SEGURO)
    ============================================ */
    
    /**
     * Registrar verificación desde QR (MÉTODO ACTUALIZADO)
     * 🔧 CORRECCIÓN: Ya no se usa directamente, se usa desde el controlador
     * Pero lo mantenemos por compatibilidad
     */
    public static function registrarDesdeQR($codigoQr)
    {
        try {
            $usuario = Auth::user();
            $ahora = Carbon::now();

            if (!$usuario) {
                return ['error' => 'Usuario no autenticado.'];
            }

            // Buscar puesto por código QR (esto debería venir del controlador)
            $puesto = PuestoSeguridad::where('codigo', $codigoQr)->first();
            
            if (!$puesto) {
                return ['error' => 'Código QR no pertenece a un puesto válido.'];
            }

            // Buscar turno activo
            $turno = RegistrarTurno::where('colaborador_id', $usuario->colaborador_id ?? $usuario->id)
                ->where('puesto_seguridad_id', $puesto->id)
                ->whereDate('fecha', $ahora->toDateString())
                ->whereTime('hora_inicio', '<=', $ahora->toTimeString())
                ->whereTime('hora_fin', '>=', $ahora->toTimeString())
                ->first();

            if (!$turno) {
                return ['error' => 'No hay turno activo para este puesto en este momento.'];
            }

            // Determinar tipo de verificación
            $ultima = self::where('registrar_turno_id', $turno->id)
                ->where('estado', 'verificado')
                ->orderBy('hora_verificacion', 'desc')
                ->first();

            $tipo = 'ingreso'; // Por defecto
            
            if ($ultima) {
                $tipo = match($ultima->tipo) {
                    'ingreso' => 'ronda',
                    'ronda' => 'ronda',
                    'salida' => 'ingreso', // Nuevo día, nuevo ingreso
                    default => 'ingreso'
                };
                
                // Si ya es hora de salida
                $horaFin = Carbon::parse($turno->hora_fin);
                if ($ahora->greaterThanOrEqualTo($horaFin) && $ultima->tipo !== 'salida') {
                    $tipo = 'salida';
                }
            }

            // Crear verificación
            $verificacion = self::create([
                'registrar_turno_id' => $turno->id,
                'tipo' => $tipo,
                'estado' => 'verificado', // Ahora se verifica automáticamente
                'hora_verificacion' => $ahora,
                'verificado_por' => $usuario->id,
                'observacion' => "Verificación {$tipo} vía QR"
            ]);

            Log::info('Verificación QR registrada', [
                'verificacion_id' => $verificacion->id,
                'tipo' => $tipo,
                'puesto' => $puesto->codigo,
                'usuario' => $usuario->id
            ]);

            return [
                'success' => true,
                'tipo' => $tipo,
                'mensaje' => match($tipo) {
                    'ingreso' => '✅ Ingreso registrado correctamente',
                    'salida' => '✅ Salida registrada correctamente',
                    'ronda' => '✅ Ronda de verificación registrada',
                    default => '✅ Verificación registrada'
                },
                'verificacion' => $verificacion,
                'data' => [
                    'id' => $verificacion->id,
                    'hora' => $verificacion->hora_formateada,
                    'puesto' => $puesto->puesto,
                    'codigo_puesto' => $puesto->codigo
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error en registrarDesdeQR: ' . $e->getMessage());
            
            return [
                'error' => 'Error interno al procesar el QR.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /* ============================================
       MÉTODOS PARA FILAMENT / UI
    ============================================ */
    
    /**
     * Obtener color para badges según tipo
     */
    public function getColorTipoAttribute()
    {
        return match($this->tipo) {
            'ingreso' => 'primary',
            'salida' => 'success',
            'ronda' => 'warning',
            'reemplazo' => 'info',
            default => 'gray'
        };
    }

    /**
     * Obtener color para badges según estado
     */
    public function getColorEstadoAttribute()
    {
        return match($this->estado) {
            'verificado' => 'success',
            'pendiente' => 'warning',
            'cerrado' => 'danger',
            default => 'gray'
        };
    }

    /**
     * Obtener icono según tipo
     */
    public function getIconoTipoAttribute()
    {
        return match($this->tipo) {
            'ingreso' => 'heroicon-o-arrow-right-circle',
            'salida' => 'heroicon-o-arrow-left-circle',
            'ronda' => 'heroicon-o-shield-check',
            'reemplazo' => 'heroicon-o-user-plus',
            default => 'heroicon-o-question-mark-circle'
        };
    }

    /**
     * Verificar si puede ser editada
     */
    public function puedeEditar()
    {
        // Solo se pueden editar verificaciones pendientes
        // y que no hayan expirado
        return $this->estado === 'pendiente' && !$this->estaExpirada();
    }

    

    
}
