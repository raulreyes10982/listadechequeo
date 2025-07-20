<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReporteTecnico extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'equipo_id',
        'tipo_intervencion_id',
        'subidopor',
    ];

    protected static function booted()
    {
        // • Completar datos básicos
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha     ??= Carbon::now()->toDateString();
            $reporte->hora      ??= Carbon::now()->format('H:i:s');
        });

        // • Crear 1er historial automáticamente con estado “Pendiente” (id = 1)
        static::created(function ($reporte) {
            $reporte->historialEstadoReportes()->create([
                'estado_reporte_id' => 1,          // asegúrate de que “Pendiente” sea ID=1
                'cambiado_por'      => $reporte->subidopor,
                'fecha'             => $reporte->fecha,
                'hora'              => $reporte->hora,
            ]);
        });
    }

    

    /* ---------- Relaciones ---------- */
    public function equipo()                 { return $this->belongsTo(Equipo::class); }
    public function tipoIntervencion()       { return $this->belongsTo(TipoIntervencion::class); }

    public function historialEstadoReportes()
    {
        return $this->hasMany(HistorialEstadoReporte::class);
    }

    public function ultimoEstado()           { return $this->hasOne(HistorialEstadoReporte::class)->latestOfMany(); }

    

}
