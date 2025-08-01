<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoReporte extends Model
{
    protected $fillable = [
        'reporte_id',
        'estado_id',
        'descripcion',
        'registrado_por',
        'fecha',
        'hora',
    ];

    public function reporte()
    {
        return $this->belongsTo(Reporte::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
    
}
