<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $codigo
 * @property int|null $piso
 * @property string|null $modulo
 * @property int|null $categoria_local_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CategoriaLocal|null $categoriaLocal
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereCategoriaLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereModulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura wherePiso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nomenclatura whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

