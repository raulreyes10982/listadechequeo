<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seguimiento extends Model
{
    use HasFactory;

    protected $fillable = ['reporte_id', 'descripcion'];

    public function reporte()
    {
        return $this->belongsTo(Reporte::class);
    }
}
