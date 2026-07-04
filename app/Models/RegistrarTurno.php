<?php

namespace App\Models;

use App\Rules\SinSolapamientoTurno;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

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
    */
    protected static function booted(): void
    {
        // Asignar fecha si no viene
        static::creating(function ($turno) {
            if (empty($turno->fecha)) {
                $turno->fecha = now()->toDateString();
            }
        });

        // ✅ Validar solapamiento ANTES de crear
        static::creating(function ($turno) {
            static::validarSolapamiento($turno);
        });

        // ✅ Validar solapamiento ANTES de actualizar (si cambian campos clave)
        static::updating(function ($turno) {
            if ($turno->isDirty(['fecha', 'hora_inicio', 'hora_fin', 'puesto_seguridad_id', 'colaborador_id'])) {
                static::validarSolapamiento($turno, $turno->id);
            }
        });

        // Crear verificaciones de ingreso y salida al crear el turno
        static::created(function ($turno) {
            $turno->verificaciones()->create([
                'tipo'              => 'ingreso',
                'estado'            => 'pendiente',
                'hora_verificacion' => null,
                'verificado_por'    => null,
                'observacion'       => null,
            ]);

            $turno->verificaciones()->create([
                'tipo'              => 'salida',
                'estado'            => 'pendiente',
                'hora_verificacion' => null,
                'verificado_por'    => null,
                'observacion'       => null,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Validar solapamiento — lanza ValidationException si hay conflicto
    |--------------------------------------------------------------------------
    */
    protected static function validarSolapamiento(self $turno, ?int $excludeId = null): void
    {
        $regla = new SinSolapamientoTurno(
            fecha:         $turno->fecha,
            horaInicio:    $turno->hora_inicio,
            horaFin:       $turno->hora_fin,
            puestoId:      $turno->puesto_seguridad_id,
            colaboradorId: $turno->colaborador_id,
            excludeId:     $excludeId,
        );

        $validator = Validator::make(
            ['turno' => true],
            ['turno' => [$regla]]
        );

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'hora_inicio' => $validator->errors()->first('turno'),
            ]);
        }
    }

}
