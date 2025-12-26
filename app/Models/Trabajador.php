<?php

namespace App\Models;

use App\Models\VerificacionDiaria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
