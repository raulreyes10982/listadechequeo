<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\VerificacionDiaria;
use App\Models\Trabajador;

class Permiso extends Model
{
    use HasFactory;

    protected $fillable = [
        'subidopor',
        'fecha_inicio_trabajo',
        'fecha_fin_trabajo',
        'descripcion',
        'archivo_pdf',
        'local_id',
        'tipo_permiso_id',
        'tipo_actividad',
        'contratistas_id',
        'actividad',
        'colaborador_id',
    ];

    protected $casts = [
        'tipo_actividad' => 'array',
    ];

    protected $dates = [
        'fecha_inicio_trabajo',
        'fecha_fin_trabajo',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Model
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        // Al crear un permiso
        static::creating(function ($permiso) {
            $permiso->subidopor = Auth::user()->name ?? 'Sistema';
            $permiso->fecha_inicio_trabajo ??= now()->toDateString();
            $permiso->fecha_fin_trabajo ??= now()->toDateString();
        });

        // Después de crearlo, generar verificaciones para sus trabajadores
        static::created(function (Permiso $permiso) {
            $permiso->loadMissing('trabajadores');
            foreach ($permiso->trabajadores as $t) {
                $permiso->generarVerificacionesParaTrabajador($t);
            }
        });

        // Si se actualizan las fechas, regenerar verificaciones
        static::updated(function (Permiso $permiso) {
            if ($permiso->wasChanged(['fecha_inicio_trabajo', 'fecha_fin_trabajo'])) {
                $permiso->loadMissing('trabajadores');
                foreach ($permiso->trabajadores as $t) {
                    $permiso->generarVerificacionesParaTrabajador($t);
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class);
    }

    public function contratistas()
    {
        return $this->belongsTo(Contratistas::class, 'contratistas_id');
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }

    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }

    public function verificaciones()
    {
        return $this->hasMany(VerificacionDiaria::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accesores y Helpers
    |--------------------------------------------------------------------------
    */
    public function getDiasRestantesAttribute()
    {
        $fechaFinal = Carbon::parse($this->fecha_fin_trabajo);
        $fechaInicio = Carbon::parse($this->fecha_inicio_trabajo);
        $hoy = Carbon::now();

        if ($fechaInicio->equalTo($fechaFinal)) {
            return 1;
        }

        return $fechaFinal->greaterThan($hoy)
            ? $hoy->diffInDays($fechaFinal)
            : -$fechaFinal->diffInDays($hoy);
    }

    public function getTercerosUnidadAttribute(): string
    {
        $tercero = optional($this->contratista)->descripcion ?? optional($this->contratistas)->descripcion;

        $unidad  = optional($this->local)->option_label ?? optional($this->local)->descripcion;

        return $tercero ?: ($unidad ?: '-');
    }

    public function getDiasAutorizadosAttribute(): ?int
    {
        $inicio = $this->fecha_inicio_trabajo ?? $this->fecha_inicio ?? null;
        $fin    = $this->fecha_fin_trabajo ?? $this->fecha_fin ?? null;

        if (!$inicio || !$fin) {
            return null;
        }

        return Carbon::parse($inicio)->diffInDays(Carbon::parse($fin)) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos
    |--------------------------------------------------------------------------
    */
    public function generarVerificacionesParaTrabajador(Trabajador $trabajador): void
    {
        $inicio = Carbon::parse($this->fecha_inicio_trabajo)->startOfDay();
        $fin    = Carbon::parse($this->fecha_fin_trabajo)->startOfDay();

        if ($inicio->gt($fin)) {
            return;
        }

        for ($fecha = (clone $inicio); $fecha->lte($fin); $fecha->addDay()) {
            VerificacionDiaria::firstOrCreate(
                [
                    'permiso_id'    => $this->id,
                    'trabajador_id' => $trabajador->id,
                    'fecha'         => $fecha->toDateString(),
                ],
                [
                    'nombre'     => $trabajador->nombre,
                    'documento'  => $trabajador->documento,
                    'estado'     => 'Vigente',
                    'verificado' => false,
                ]
            );
        }
    }

    
}

