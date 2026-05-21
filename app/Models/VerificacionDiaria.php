<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class VerificacionDiaria extends Model
{
    use HasFactory;

    protected $table = 'verificacion_diarias';

    protected $fillable = [
        'permiso_id',
        'trabajador_id',
        'colaborador_id',
        'fecha',
        'hora',
        'verificadopor',
        'nombre',
        'documento',
        'estado',
        'dias_autorizados',
        'verificado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'verificado' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($verificacion) {
            $verificacion->verificadopor = Auth::user()->name ?? 'Sistema';
            $verificacion->fecha ??= now()->toDateString();
            $verificacion->hora ??= now()->toTimeString();
        });

        static::saved(function ($verificacion) {
            if ($verificacion->wasChanged('verificado') && $verificacion->verificado) {
                HistorialVerificacion::create([
                    'permiso_id' => $verificacion->permiso_id,
                    'trabajador_id' => $verificacion->trabajador_id,
                    'nombre' => $verificacion->nombre,
                    'documento' => $verificacion->documento,
                    'estado' => $verificacion->estado,
                    'dias_autorizados' => $verificacion->dias_autorizados,
                    'verificado' => $verificacion->verificado,
                    'fecha' => $verificacion->fecha ?? now()->toDateString(),
                    'hora' => $verificacion->hora ?? now()->toTimeString(),
                ]);
            }
        });
    }

    public function permiso(): BelongsTo
    {
        return $this->belongsTo(Permiso::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function scopeHoyNoVerificado($query)
    {
        return $query
            ->whereDate('fecha', now()->toDateString())
            ->where('verificado', false);
    }

    public function scopeVerificados($query)
    {
        return $query->where('verificado', true);
    }

    /**
     * Días autorizados restantes (calculado desde el permiso).
     */
    public function getDiasRestantesAttribute(): ?int
    {
        $fin = optional($this->permiso)->fecha_fin_trabajo
            ? Carbon::parse($this->permiso->fecha_fin_trabajo)->startOfDay()
            : null;

        if (! $fin) {
            return null;
        }

        $hoy = Carbon::today();
        $diff = $hoy->diffInDays($fin, false);

        return $diff >= 0 ? $diff + 1 : $diff;
    }

    /**
     * Estado dinámico según fecha de fin del permiso.
     */
    public function getEstadoAttribute($value): string
    {
        $fin = $this->permiso?->fecha_fin_trabajo;

        if (! $fin) {
            return $value ?? '-';
        }

        return Carbon::today()->lte(Carbon::parse($fin))
            ? 'Autorizado'
            : 'Vencido';
    }
}
