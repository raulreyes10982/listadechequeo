<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
    ];

    /**
     * Relación: Un departamento tiene muchas áreas
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}

