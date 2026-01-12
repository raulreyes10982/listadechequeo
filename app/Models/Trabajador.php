<?php

namespace App\Models;

use App\Models\VerificacionDiaria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nombre
 * @property string $documento
 * @property int $permiso_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Permiso $permiso
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VerificacionDiaria> $verificacionesDiarias
 * @property-read int|null $verificaciones_diarias_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereDocumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador wherePermisoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trabajador whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'permiso_id',
        'documento',
        'nombre',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Model
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        // Cuando se crea un trabajador, generar sus verificaciones
        static::created(function (Trabajador $t) {
            $t->permiso?->generarVerificacionesParaTrabajador($t);
        });

        // Al eliminar un trabajador, eliminar sus verificaciones asociadas
        static::deleting(function (Trabajador $t) {
            $t->verificacionesDiarias()->delete();
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

    public function verificacionesDiarias()
    {
        return $this->hasMany(VerificacionDiaria::class);
    }
}
