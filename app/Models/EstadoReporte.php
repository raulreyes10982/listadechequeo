<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class EstadoReporte extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstadoReporte::class);
    }
}
