<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Nomenclatura extends Model
{
    
    // Nombre de la tabla (opcional si sigue la convención)
    protected $table = 'nomenclaturas';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'codigo',
        'piso',
        'modulo',
        'categoria_local_id',
        ]; 

    public function categoriaLocal()
    {
        return $this->belongsTo(CategoriaLocal::class);
    }

    
}

