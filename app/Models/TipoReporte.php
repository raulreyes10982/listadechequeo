<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property int $categoria_reporte_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CategoriaReporte $categoria
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte whereCategoriaReporteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoReporte whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoReporte extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'categoria_reporte_id'];

    public function categoria()
    {
        return $this->belongsTo(CategoriaReporte::class, 'categoria_reporte_id');
    }
}
