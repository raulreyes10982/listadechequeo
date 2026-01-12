<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property int $area_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area $area
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cargo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'area_id',
    ];

    /**
     * Relación: un cargo pertenece a un área
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    
}
