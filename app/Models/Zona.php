<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zonas'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
