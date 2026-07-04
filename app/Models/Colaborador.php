<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colaborador extends Model
{
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'celular',
        'documento',
        'lugarnacimiento',
        'telefono',
        'fecha_nacimiento',
        'edad',
        'barrio',
        'direccion',
        'correo_corporativo',
        'correo_personal',
        'fechainiciolab',
        'fechafinlab',
        'tipo_documento_id',
        'estado_civil_id',
        'departamento_id',
        'area_id',
        'cargo_id',
        'tipo_contrato_id',
        'genero_id',
        'grupo_sanguineo_id',
    ];

    protected $casts = [
        // ✅ Campos que eran integer — ahora son string en la BD
        'celular'          => 'string',
        'telefono'         => 'string',
        'documento'        => 'string',
        // Fechas
        'fecha_nacimiento' => 'date',
        'fechainiciolab'   => 'date',
        'fechafinlab'      => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT — calcular edad automáticamente
    |--------------------------------------------------------------------------
    | Se dispara al CREAR y al ACTUALIZAR si cambia la fecha de nacimiento.
    | Así la columna "edad" siempre está sincronizada sin intervención manual.
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        // Al crear: calcular edad desde fecha_nacimiento si se provee
        static::creating(function (Colaborador $colaborador) {
            if ($colaborador->fecha_nacimiento) {
                $colaborador->edad = Carbon::parse($colaborador->fecha_nacimiento)->age;
            }
        });

        // Al actualizar: recalcular solo si cambió la fecha de nacimiento
        static::updating(function (Colaborador $colaborador) {
            if ($colaborador->isDirty('fecha_nacimiento') && $colaborador->fecha_nacimiento) {
                $colaborador->edad = Carbon::parse($colaborador->fecha_nacimiento)->age;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */
    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function estadoCivil(): BelongsTo
    {
        return $this->belongsTo(EstadoCivil::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function tipoContrato(): BelongsTo
    {
        return $this->belongsTo(TipoContrato::class);
    }

    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    public function grupoSanguineo(): BelongsTo
    {
        return $this->belongsTo(GrupoSanguineo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentos(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(ColaboradorDocumento::class);
}
}
