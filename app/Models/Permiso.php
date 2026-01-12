<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\VerificacionDiaria;
use App\Models\Trabajador;

/**
 * @property int $id
 * @property string|null $subidopor
 * @property string $fecha_inicio_trabajo
 * @property string $fecha_fin_trabajo
 * @property string|null $descripcion
 * @property string|null $actividad
 * @property array<array-key, mixed>|null $tipo_actividad
 * @property string|null $archivo_pdf
 * @property int|null $local_id
 * @property int|null $contratistas_id
 * @property int|null $tipo_permiso_id
 * @property int|null $colaborador_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Colaborador|null $colaborador
 * @property-read \App\Models\Contratistas|null $contratistas
 * @property-read int|null $dias_autorizados
 * @property-read mixed $dias_restantes
 * @property-read string $terceros_unidad
 * @property-read \App\Models\Local|null $local
 * @property-read \App\Models\TipoPermiso|null $tipoPermiso
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Trabajador> $trabajadores
 * @property-read int|null $trabajadores_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VerificacionDiaria> $verificaciones
 * @property-read int|null $verificaciones_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereArchivoPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereColaboradorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereContratistasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereFechaFinTrabajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereFechaInicioTrabajo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereSubidopor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereTipoActividad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereTipoPermisoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permiso whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

