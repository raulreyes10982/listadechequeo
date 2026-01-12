<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property int $tipo_equipo_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Colaborador|null $colaborador
 * @property-read mixed $option_label
 * @property-read \App\Models\TipoEquipo $tipoEquipo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo whereTipoEquipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Equipo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
