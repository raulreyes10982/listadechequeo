<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEquipo extends Model
{
    //
    protected $fillable = [
        'descripcion', 
    ];

    protected $table = 'tipo_equipos';
}
