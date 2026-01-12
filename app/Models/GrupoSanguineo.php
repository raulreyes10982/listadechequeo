<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Colaborador> $colaboradores
 * @property-read int|null $colaboradores_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupoSanguineo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GrupoSanguineo extends Model
{
    protected $table = 'grupo_sanguineos';

    protected $fillable = ['descripcion'];

    public function colaboradores()
    {
        return $this->hasMany(Colaborador::class, 'grupo_sanguineo_id');
    }
}