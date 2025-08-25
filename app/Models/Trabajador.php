<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    // Usamos nombre de tabla en plural español
    protected $table = 'trabajadores';

    protected $fillable = [
        'permiso_id',
        'documento',
        'nombre',
    ];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
