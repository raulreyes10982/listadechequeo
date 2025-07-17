<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'imagenes',
        'subidopor',
        'categoria_reporte_id',
        'tipo_reporte_id',
        'equipo_id',
    ];

    protected $casts = [
        'imagenes' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= Carbon::now()->toDateString();
            $reporte->hora ??= Carbon::now()->format('H:i:s');
        });
    }

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaReporte::class, 'categoria_reporte_id');
    }

    public function tipoReporte()
    {
        return $this->belongsTo(TipoReporte::class, 'tipo_reporte_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
