<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class HistorialVerificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'permiso_id',
        'trabajador_id',
        'fecha',
        'hora',
        'verificadopor',
        'nombre',
        'documento',
        'estado',
        'dias_autorizados',
        'verificado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'verificado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($historial) {
            $historial->verificadopor ??= Auth::user()->name ?? 'Sistema';
            $historial->fecha ??= now()->toDateString();
            $historial->hora ??= now()->toTimeString();
        });
    }

    public function permiso()
    {
        return $this->belongsTo(\App\Models\Permiso::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(\App\Models\Trabajador::class);
    }
}
