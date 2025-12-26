<?php
// app/Models/Local.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 
        'nomenclatura_id',
        'categoria_local_id',
    ];

    public function nomenclatura()
    {
        return $this->belongsTo(Nomenclatura::class);
    }

    public function getOptionLabelAttribute()
{
    return ($this->nomenclatura?->categoriaLocal?->descripcion ?? 'Sin categoría')
        . ' ' . ($this->nomenclatura?->codigo ?? 'Sin código')
        . ' - ' . $this->nombre;
}
}
