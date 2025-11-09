<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistorialEstadoReporte extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'reporte_tecnico_id',
        'estado_reporte_id',
        'fecha',
        'hora',
        'cambiado_por',
        'descripcion',
    ];

    protected static function booted()
    {
        static::creating(function ($hist) {
            $hist->cambiado_por ??= Auth::user()->name ?? 'Sistema';
            $hist->fecha        ??= Carbon::now()->toDateString();
            $hist->hora         ??= Carbon::now()->format('H:i:s');
        });
    }

    /* ---------- Relaciones ---------- */
    public function reporteTecnico()  { return $this->belongsTo(ReporteTecnico::class); }
    public function estadoReporte()   { return $this->belongsTo(EstadoReporte::class); }
}
