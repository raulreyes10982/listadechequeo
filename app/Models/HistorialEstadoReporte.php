<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $cambiado_por
 * @property string $fecha
 * @property string $hora
 * @property string|null $descripcion
 * @property int $reporte_tecnico_id
 * @property int $estado_reporte_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\EstadoReporte $estadoReporte
 * @property-read \App\Models\ReporteTecnico $reporteTecnico
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereCambiadoPor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereEstadoReporteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereReporteTecnicoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialEstadoReporte whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
