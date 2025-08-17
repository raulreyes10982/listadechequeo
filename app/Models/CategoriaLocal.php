<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaLocal extends Model
{
    protected $table = 'categoria_locals';

    protected $fillable = ['descripcion'];
}
