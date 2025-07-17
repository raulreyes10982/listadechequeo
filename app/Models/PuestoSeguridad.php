<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuestoSeguridad extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'puesto_seguridads';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'codigo',
        'puesto',
        'inicio_hora',
        'fin_hora',
        'descripcion',
    ];

    // Si quieres tratar las horas como Carbon instances (para formatearlas fácilmente)
    protected $casts = [
        'inicio_hora' => 'datetime:H:i',
        'fin_hora' => 'datetime:H:i',
    ];
}