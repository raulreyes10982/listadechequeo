<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TipoReporte> $tipoReportes
 * @property-read int|null $tipo_reportes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaReporte whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoriaReporte extends Model
{
    use HasFactory;

    protected $table = 'categorias_reporte';

    protected $fillable = ['descripcion'];

    public function tipoReportes()
    {
        return $this->hasMany(TipoReporte::class, 'categoria_reporte_id');
    }
}
