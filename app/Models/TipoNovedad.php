<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoNovedad extends Model
{
    //
    protected $table = 'tipo_novedads'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
