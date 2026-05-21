<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipo extends Model
{
    protected $fillable = [
        'descripcion',
        'tipo_equipo_id',
    ];

    public function tipoEquipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_equipo_id');
    }

    public function getOptionLabelAttribute(): string
    {
        return $this->tipoEquipo->descripcion.' - '.$this->descripcion;
    }
}
