<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property string|null $descripcion
 * @property int $departamento_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cargo> $cargos
 * @property-read int|null $cargos_count
 * @property-read \App\Models\Departamento $departamento
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'departamento_id',
    ];

    /**
     * Relación: un área pertenece a un departamento
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Relación: un área tiene muchos cargos
     */
    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }
}

