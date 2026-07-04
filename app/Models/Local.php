<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nomenclatura_id',
        'categoria_local_id',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function nomenclatura()
    {
        return $this->belongsTo(Nomenclatura::class);
    }

    public function categoriaLocal()
    {
        return $this->belongsTo(CategoriaLocal::class);
    }

    public function getOptionLabelAttribute()
    {
        return ($this->nomenclatura?->categoriaLocal?->descripcion ?? 'Sin categoría')
            . ' ' . ($this->nomenclatura?->codigo ?? 'Sin código')
            . ' - ' . $this->nombre;
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }
}
