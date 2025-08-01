<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prioridad extends Model
{
    //
    protected $table = 'prioridads'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
