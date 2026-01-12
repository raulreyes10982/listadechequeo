<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ubicacion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ubicacion extends Model
{
    protected $table = 'ubicacions'; // 
    protected $fillable = ['descripcion'];
}
