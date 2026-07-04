<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CategoriaLocal extends Model
{
    protected $table = 'categoria_locals';

    protected $fillable = ['descripcion', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function nomenclaturas()
    {
        return $this->hasMany(Nomenclatura::class);
    }

    public function locals()
    {
        return $this->hasMany(Local::class);
    }

    // ✅ Scopes para filtrar activos/inactivos fácilmente
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }
}
