<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
