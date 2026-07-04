<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Nomenclatura extends Model
{
    protected $table = 'nomenclaturas';

    protected $fillable = [
        'codigo',
        'piso',
        'modulo',
        'categoria_local_id',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function categoriaLocal()
    {
        return $this->belongsTo(CategoriaLocal::class);
    }

    public function locals()
    {
        return $this->hasMany(Local::class);
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
