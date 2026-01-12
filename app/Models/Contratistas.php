<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contratistas whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contratistas extends Model
{

    protected $table = 'contratistas';

    protected $fillable = ['descripcion'];


}
