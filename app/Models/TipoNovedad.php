<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoNovedad whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoNovedad extends Model
{
    //
    protected $table = 'tipo_novedads'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
