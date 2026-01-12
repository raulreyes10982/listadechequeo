<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $fecha
 * @property string $hora
 * @property string $registrado_por
 * @property string|null $descripcion
 * @property int|null $reporte_id
 * @property int|null $estado_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Estado|null $estado
 * @property-read \App\Models\Reporte|null $reporte
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereEstadoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereRegistradoPor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereReporteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BitacoraEstado whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BitacoraEstado extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'reporte_id',
        'estado_id',
        'descripcion',
        'registrado_por',
        'fecha',
        'hora',
    ];

    protected static function booted()
    {
        static::creating(function ($hist) {
            $hist->registrado_por ??= Auth::user()->name ?? 'Sistema';
            $hist->fecha ??= Carbon::now()->toDateString();
            $hist->hora ??= Carbon::now()->format('H:i:s');
        });
    }

    public function reporte(): BelongsTo { return $this->belongsTo(Reporte::class); }
    public function estado(): BelongsTo  { return $this->belongsTo(Estado::class, 'estado_id'); }
}
