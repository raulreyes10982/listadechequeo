<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * ✅ Relación: un cargo es ocupado por muchos colaboradores
     * Necesaria para mostrar el contador y bloquear eliminación
     */
    public function colaboradores(): HasMany
    {
        return $this->hasMany(Colaborador::class, 'cargo_id');
    }
}
