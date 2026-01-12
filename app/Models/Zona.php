<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Zona whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Zona extends Model
{
    protected $table = 'zonas'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
