<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
