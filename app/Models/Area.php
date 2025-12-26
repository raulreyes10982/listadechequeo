<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

