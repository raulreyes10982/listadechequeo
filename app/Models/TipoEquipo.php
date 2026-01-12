<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEquipo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoEquipo extends Model
{
    //
    protected $fillable = [
        'descripcion', 
    ];

    protected $table = 'tipo_equipos';
}
