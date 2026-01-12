<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoCivil whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EstadoCivil extends Model
{
    protected $table = 'estado_civils'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
