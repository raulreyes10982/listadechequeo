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

    /**
     * Boot model events.
     */
    protected static function booted()
    {
        // Completar datos básicos antes de guardar
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha     ??= Carbon::now()->toDateString();
            $reporte->hora      ??= Carbon::now()->format('H:i:s');
        });

        // Crear historial inicial con estado "Pendiente" automáticamente
        static::created(function ($reporte) {
            $reporte->historialEstadoReportes()->create([
                'estado_reporte_id' => 1, // Asegúrate de que ID=1 sea "Pendiente"
                'cambiado_por'      => $reporte->subidopor,
                'fecha'             => $reporte->fecha,
                'hora'              => $reporte->hora,
                'descripcion'       => $reporte->descripcion,
            ]);
        });
    }

    /* ---------------- Relaciones ---------------- */

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tipoIntervencion()
    {
        return $this->belongsTo(TipoIntervencion::class);
    }

    public function ultimoEstado()
    {
        return $this->hasOne(HistorialEstadoReporte::class)->latestOfMany();
    }

    public function historialEstadoReportes()
    {
        return $this->hasMany(HistorialEstadoReporte::class);
    }
}

