<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReporteTecnico> $reportesTecnicos
 * @property-read int|null $reportes_tecnicos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoIntervencion whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TipoIntervencion extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function reportesTecnicos()
    {
        return $this->hasMany(ReporteTecnico::class);
    }
}
