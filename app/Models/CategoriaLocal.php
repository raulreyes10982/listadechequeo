<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CategoriaLocal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoriaLocal extends Model
{
    protected $table = 'categoria_locals';

    protected $fillable = ['descripcion'];
}
