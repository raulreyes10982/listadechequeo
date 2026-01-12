<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Estado whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Estado extends Model
{
    //
    protected $table = 'estados'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
