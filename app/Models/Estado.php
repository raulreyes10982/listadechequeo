<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    //
    protected $table = 'estados'; // 👈 Solución aquí

    protected $fillable = ['descripcion'];
}
