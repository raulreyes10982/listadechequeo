<?php
// app/Models/Local.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nombre
 * @property int|null $nomenclatura_id
 * @property int|null $categoria_local_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $option_label
 * @property-read \App\Models\Nomenclatura|null $nomenclatura
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereCategoriaLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereNomenclaturaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Local whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'nomenclatura_id',
        'categoria_local_id',
    ];

    public function nomenclatura()
    {
        return $this->belongsTo(Nomenclatura::class);
    }

    public function getOptionLabelAttribute()
{
    return ($this->nomenclatura?->categoriaLocal?->descripcion ?? 'Sin categoría')
        . ' ' . ($this->nomenclatura?->codigo ?? 'Sin código')
        . ' - ' . $this->nombre;
}
}
