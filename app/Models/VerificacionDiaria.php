<?php 

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'fecha'      => 'date',
        'verificado' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Model
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        // Antes de crear el registro
        static::creating(function ($reporte) {
            $reporte->verificadopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= now()->toDateString();
            $reporte->hora  ??= now()->toTimeString();
        });

        // Después de guardar, si se marcó como verificado -> crear historial
        static::saved(function ($verificacion) {
            if ($verificacion->wasChanged('verificado') && $verificacion->verificado) {
                \App\Models\HistorialVerificacion::create([
                    'permiso_id'       => $verificacion->permiso_id,
                    'trabajador_id'    => $verificacion->trabajador_id,
                    'nombre'           => $verificacion->nombre,
                    'documento'        => $verificacion->documento,
                    'estado'           => $verificacion->estado,
                    'dias_autorizados' => $verificacion->dias_autorizados,
                    'verificado'       => $verificacion->verificado,
                    'fecha'            => $verificacion->fecha ?? now()->toDateString(),
                    'hora'             => $verificacion->hora  ?? now()->toTimeString(),
                ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */
    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }

    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function contratistas()
    {
        return $this->belongsTo(Contratistas::class, 'contratistas_id');
    }

    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeHoyNoVerificado($query)
    {
        return $query
            ->whereDate('fecha', now()->toDateString())
            ->where('verificado', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Accesores
    |--------------------------------------------------------------------------
    */
    /**
     * Días autorizados restantes (desde hoy hasta fecha fin_trabajo inclusivo).
     */
    public function getDiasAutorizadosAttribute()
    {
        $fin = optional($this->permiso)->fecha_fin_trabajo
            ? Carbon::parse($this->permiso->fecha_fin_trabajo)->startOfDay()
            : null;

        if (! $fin) {
            return null;
        }

        $hoy  = Carbon::today();
        $diff = $hoy->diffInDays($fin, false);

        // Inclusivo: si hoy es el último día, muestra 1
        return $diff >= 0 ? $diff + 1 : $diff; // negativos = vencido
    }

    /**
     * Estado dinámico: 'Vigente' o 'Vencido' según fecha_fin_trabajo.
     */
    public function getEstadoAttribute($value)
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

