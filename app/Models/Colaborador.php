<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colaborador extends Model
{
    protected $fillable = [
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

    public function grupoSanguineo()
    {
        return $this->belongsTo(GrupoSanguineo::class);
    }
}
