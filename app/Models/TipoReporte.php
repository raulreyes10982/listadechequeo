<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoReporte extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'categoria_reporte_id'];

    public function categoria()
    {
        return $this->belongsTo(CategoriaReporte::class, 'categoria_reporte_id');
    }
}
