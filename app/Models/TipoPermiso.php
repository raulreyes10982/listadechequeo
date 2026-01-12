<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoPermiso whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoPermiso extends Model
{
    // Tabla asociada al modelo
    protected $table = 'tipo_permisos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'descripcion',
    ];
    
}
