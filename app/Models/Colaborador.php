<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $nombre
 * @property string|null $apellido
 * @property int|null $celular
 * @property int|null $documento
 * @property string|null $lugarnacimiento
 * @property int|null $telefono
 * @property string|null $fecha_nacimiento
 * @property int|null $edad
 * @property string|null $barrio
 * @property string|null $direccion
 * @property string $correo_corporativo
 * @property string $correo_personal
 * @property string|null $fechainiciolab
 * @property string|null $fechafinlab
 * @property int $tipo_documento_id
 * @property int $estado_civil_id
 * @property int $departamento_id
 * @property int $area_id
 * @property int $cargo_id
 * @property int $tipo_contrato_id
 * @property int $genero_id
 * @property int $grupo_sanguineo_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area $area
 * @property-read \App\Models\Cargo $cargo
 * @property-read \App\Models\Departamento $departamento
 * @property-read \App\Models\EstadoCivil $estadoCivil
 * @property-read \App\Models\Genero $genero
 * @property-read \App\Models\GrupoSanguineo $grupoSanguineo
 * @property-read \App\Models\TipoContrato $tipoContrato
 * @property-read \App\Models\TipoDocumento $tipoDocumento
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereBarrio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCargoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCelular($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCorreoCorporativo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCorreoPersonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereDepartamentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereDocumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereEdad($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereEstadoCivilId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereFechaNacimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereFechafinlab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereFechainiciolab($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereGeneroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereGrupoSanguineoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereLugarnacimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereTipoContratoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereTipoDocumentoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Colaborador whereUserId($value)
 * @mixin \Eloquent
 */
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
