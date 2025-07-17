<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoSanguineo extends Model
{
    protected $table = 'grupo_sanguineos';

    protected $fillable = ['descripcion'];

    public function colaboradores()
    {
        return $this->hasMany(Colaborador::class, 'grupo_sanguineo_id');
    }
}