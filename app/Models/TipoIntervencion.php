<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TipoIntervencion extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function reportesTecnicos()
    {
        return $this->hasMany(ReporteTecnico::class);
    }
}
