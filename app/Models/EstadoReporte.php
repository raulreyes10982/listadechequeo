<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialEstadoReporte> $historialEstados
 * @property-read int|null $historial_estados_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoReporte whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EstadoReporte extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstadoReporte::class);
    }
}
