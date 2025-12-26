<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPermiso extends Model
{
    // Tabla asociada al modelo
    protected $table = 'tipo_permisos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'descripcion',
    ];
    
}
