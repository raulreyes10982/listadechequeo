<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'area_id',
    ];

    /**
     * Relación: un cargo pertenece a un área
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    
}
