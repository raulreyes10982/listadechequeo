<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaReporte extends Model
{
    use HasFactory;

    protected $table = 'categorias_reporte';

    protected $fillable = ['descripcion'];

    public function tipoReportes()
    {
        return $this->hasMany(TipoReporte::class, 'categoria_reporte_id');
    }
}
