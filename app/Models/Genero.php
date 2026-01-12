<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Genero whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Genero extends Model
{
    protected $table = 'generos'; // ✅ Esto soluciona el error

    protected $fillable = ['descripcion'];
}
