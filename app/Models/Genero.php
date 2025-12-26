<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    protected $table = 'generos'; // ✅ Esto soluciona el error

    protected $fillable = ['descripcion'];
}
