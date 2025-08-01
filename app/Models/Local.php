<?php
// app/Models/Local.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'nomenclatura_id'];

    public function nomenclatura()
    {
        return $this->belongsTo(Nomenclatura::class);
    }

    public function getOptionLabelAttribute()
    {
        return $this->nomenclatura->codigo . ' - ' . $this->nombre;
    }
}
