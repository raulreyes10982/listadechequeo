<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estado_civils'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
