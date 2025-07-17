<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


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

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tipoIntervencion()
    {
        return $this->belongsTo(TipoIntervencion::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstadoReporte::class);
    }

    public function ultimoEstado()
    {
        return $this->hasOne(HistorialEstadoReporte::class)->latestOfMany();
    }

    protected static function booted()
    {
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= Carbon::now()->toDateString();
            $reporte->hora ??= Carbon::now()->format('H:i:s');
        });
    }

}
