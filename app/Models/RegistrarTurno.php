<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrarTurno extends Model
{
    protected $fillable = [
        'puesto_seguridad_id',
        'colaborador_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'observacion',
    ];

    public function puestoSeguridad()
    {
        return $this->belongsTo(PuestoSeguridad::class, 'puesto_seguridad_id');
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }
}
