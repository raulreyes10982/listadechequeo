<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $fecha
 * @property string $hora
 * @property string|null $descripcion
 * @property int|null $equipo_id
 * @property int|null $tipo_intervencion_id
 * @property string|null $subidopor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Equipo|null $equipo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HistorialEstadoReporte> $historialEstadoReportes
 * @property-read int|null $historial_estado_reportes_count
 * @property-read \App\Models\TipoIntervencion|null $tipoIntervencion
 * @property-read \App\Models\HistorialEstadoReporte|null $ultimoEstado
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereEquipoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereSubidopor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereTipoIntervencionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ReporteTecnico whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

