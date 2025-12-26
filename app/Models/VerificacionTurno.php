<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerificacionTurno extends Model
{
    use HasFactory;

    protected $fillable = [
        'registrar_turno_id',
        'tipo',
        'hora_verificacion',
        'observacion',
        'verificado_por',
        'estado',
    ];

    /* Relaciones */
    public function turno() {
        return $this->belongsTo(RegistrarTurno::class, 'registrar_turno_id');
    }

    public function verificador() {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    /* Boot: asignar automáticamente verificador y hora */
    protected static function booted() {
        static::creating(function ($verificacion) {
            if (Auth::check() && $verificacion->estado === 'verificado') {
                $verificacion->verificado_por = Auth::id();
                $verificacion->hora_verificacion = now();
            }
        });
    }

    /* --- Lógica de verificación QR --- */
    public static function registrarDesdeQR($codigoQr)
    {
        $usuario = Auth::user();
        $ahora = Carbon::now();

        $puesto = PuestoSeguridad::where('codigo_qr', $codigoQr)->first();
        if (! $puesto) {
            return ['error' => 'Código QR no pertenece a un puesto válido.'];
        }

        $turno = RegistrarTurno::where('colaborador_id', $usuario->id)
            ->where('puesto_seguridad_id', $puesto->id)
            ->whereDate('fecha', $ahora->toDateString())
            ->first();

        if (! $turno) {
            return ['error' => 'No hay turno activo para hoy.'];
        }

        // Último registro de verificación
        $ultima = self::where('registrar_turno_id', $turno->id)
            ->orderBy('hora_verificacion', 'desc')
            ->first();

        $tipo = match (optional($ultima)->tipo) {
            'ingreso' => 'salida',
            'salida'  => null,
            default   => 'ingreso',
        };

        if ($tipo === null) {
            return ['error' => 'Ya se registró ingreso y salida para este turno.'];
        }

        $verificacion = self::create([
            'registrar_turno_id' => $turno->id,
            'tipo' => $tipo,
            'estado' => 'pendiente',
        ]);

        return [
            'success' => true,
            'tipo' => $tipo,
            'mensaje' => "Registro de {$tipo} creado. Pendiente de confirmación.",
            'verificacion' => $verificacion,
        ];
    }

    public function marcarComoVerificado()
    {
        $this->update([
            'estado' => 'verificado',
            'hora_verificacion' => now(),
            'verificado_por' => Auth::id(),
        ]);
    }
}
