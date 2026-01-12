<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $fecha
 * @property string|null $hora
 * @property string|null $verificadopor
 * @property string|null $nombre
 * @property string|null $documento
 * @property string|null $estado
 * @property int|null $dias_autorizados
 * @property bool $verificado
 * @property int|null $local_id
 * @property int|null $contratistas_id
 * @property int|null $permiso_id
 * @property int|null $trabajador_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Permiso|null $permiso
 * @property-read \App\Models\Trabajador|null $trabajador
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereContratistasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereDiasAutorizados($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereDocumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion wherePermisoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereTrabajadorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereVerificado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorialVerificacion whereVerificadopor($value)
 * @mixin \Eloquent
 */
class HistorialVerificacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'permiso_id',
        'trabajador_id',
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
        static::creating(function ($historial) {
            $historial->verificadopor ??= Auth::user()->name ?? 'Sistema';
            $historial->fecha ??= now()->toDateString();
            $historial->hora ??= now()->toTimeString();
        });
    }

    public function permiso()
    {
        return $this->belongsTo(\App\Models\Permiso::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(\App\Models\Trabajador::class);
    }
}
