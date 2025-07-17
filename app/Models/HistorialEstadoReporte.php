<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class HistorialEstadoReporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporte_tecnico_id',
        'estado_reporte_id',
        'fecha_cambio',
        'cambiado_por',
    ];

    public $timestamps = false; // si no tienes created_at y updated_at

    public function reporteTecnico()
    {
        return $this->belongsTo(ReporteTecnico::class);
    }

    public function estado()
    {
        return $this->belongsTo(EstadoReporte::class, 'estado_reporte_id');
    }

    protected static function booted()
    {
        static::creating(function ($reporte) {
            $reporte->cambiado_por = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= Carbon::now()->toDateString();
            $reporte->hora ??= Carbon::now()->format('H:i:s');
        });
    }
}
