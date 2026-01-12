<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoContrato whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoContrato extends Model
{
    protected $table = 'tipo_contratos'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
