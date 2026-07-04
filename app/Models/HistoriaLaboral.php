<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo virtual para HistoriaLaboralResource.
 * Apunta a registrar_turnos — no necesita migración propia.
 */
class HistoriaLaboral extends Model
{
    protected $table = 'registrar_turnos';

    protected $casts = [
        'fecha'       => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin'    => 'datetime:H:i',
    ];

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }

    public function puestoSeguridad(): BelongsTo
    {
        return $this->belongsTo(PuestoSeguridad::class, 'puesto_seguridad_id');
    }

    public function verificaciones(): HasMany
    {
        return $this->hasMany(VerificacionTurno::class, 'registrar_turno_id');
    }
}
