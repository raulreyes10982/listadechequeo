<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string $fecha
 * @property string $hora_inicio
 * @property string $hora_fin
 * @property string|null $observacion
 * @property int $puesto_seguridad_id
 * @property int $colaborador_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Colaborador $colaborador
 * @property-read \App\Models\PuestoSeguridad $puestoSeguridad
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VerificacionTurno> $verificaciones
 * @property-read int|null $verificaciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereColaboradorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereHoraFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereHoraInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereObservacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno wherePuestoSeguridadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistrarTurno whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RegistrarTurno extends Model
{
    use HasFactory;

    protected $fillable = [
        'puesto_seguridad_id',
        'colaborador_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'observacion',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function puestoSeguridad()
    {
        return $this->belongsTo(PuestoSeguridad::class, 'puesto_seguridad_id');
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }

    public function verificaciones()
    {
        return $this->hasMany(VerificacionTurno::class, 'registrar_turno_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Eventos del Modelo
    |--------------------------------------------------------------------------
    |
    | - Si no se envía la fecha, se asigna la actual.
    | - Al crear un turno, se genera automáticamente una verificación "ingreso"
    |   con estado pendiente y sin hora/verificador asignado.
    |
    */

    protected static function booted()
    {
        static::creating(function ($turno) {
            if (empty($turno->fecha)) {
                $turno->fecha = now()->toDateString();
            }
        });

        static::created(function ($turno) {
            // Crear verificación inicial "ingreso"
            $turno->verificaciones()->create([
                'tipo' => 'ingreso',
                'estado' => 'pendiente',
                'hora_verificacion' => null,
                'verificado_por' => null,
                'observacion' => null,
            ]);
        });
    }
}
