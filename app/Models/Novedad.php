<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;



/**
 * @property int $id
 * @property string $fecha
 * @property string $hora
 * @property string|null $descripcion
 * @property string|null $subidopor
 * @property int|null $tipo_novedad_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TipoNovedad|null $tipoNovedad
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereSubidopor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereTipoNovedadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Novedad whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Novedad extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'subidopor',
        'tipo_novedad_id',
    ];

    public function tipoNovedad()
    {
        return $this->belongsTo(TipoNovedad::class);
    }

    protected static function booted()
{
    static::creating(function ($novedad) {
        if (Auth::check()) {
            $novedad->subidopor = Auth::user()->name;
        }
    }); 

}
}