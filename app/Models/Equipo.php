<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    // Añade el campo 'tipo_equipo_id' a $fillable
    protected $fillable = [
        'descripcion',
        'tipo_equipo_id',
    ];

    // Relación de un tipo equipos con muchos equipos
    public function tipoEquipo()
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_equipo_id');
    }

    // Relación con el modelo Colaborador
    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }

    /**
     * Accesor para obtener la etiqueta completa en el Select.
     */

    public function getOptionLabelAttribute()
    {
        return $this->tipoEquipo->descripcion . ' - ' . $this->descripcion;
    }
}
