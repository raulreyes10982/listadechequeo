<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prioridad whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Prioridad extends Model
{
    //
    protected $table = 'prioridads'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
