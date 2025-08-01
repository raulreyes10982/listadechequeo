<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'imagenes',
        'subidopor',
        'categoria_reporte_id',
        'tipo_reporte_id',
        'zona_id',
        'prioridad_id',
        'estado_id',
        'local_id',
        'equipo_id',
    ];

    protected $casts = [
        'imagenes' => 'array',
        'fecha' => 'date',
        'hora' => 'datetime:H:i:s',
    ];

    protected static function booted()
    {
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= Carbon::now()->toDateString();
            $reporte->hora ??= Carbon::now()->format('H:i:s');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function categoria(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CategoriaReporte::class, 'categoria_reporte_id');
    }

    public function tipoReporte(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TipoReporte::class, 'tipo_reporte_id');
    }

    public function zona(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function prioridad(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prioridad::class, 'prioridad_id');
    }

    public function estado(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // Relación con local
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function equipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoReporte::class);
    }
}
