<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    protected $table = 'tipo_contratos'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
