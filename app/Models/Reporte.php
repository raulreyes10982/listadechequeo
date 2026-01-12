<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $subidopor
 * @property \Illuminate\Support\Carbon $fecha
 * @property \Illuminate\Support\Carbon $hora
 * @property string $descripcion
 * @property array<array-key, mixed>|null $imagenes
 * @property int|null $categoria_reporte_id
 * @property int|null $tipo_reporte_id
 * @property int|null $zona_id
 * @property int|null $ubicacion_id
 * @property int|null $prioridad_id
 * @property int|null $estado_id
 * @property int|null $local_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BitacoraEstado> $bitacoras
 * @property-read int|null $bitacoras_count
 * @property-read \App\Models\CategoriaReporte|null $categoria
 * @property-read \App\Models\Estado|null $estado
 * @property-read \App\Models\Local|null $local
 * @property-read \App\Models\Prioridad|null $prioridad
 * @property-read \App\Models\TipoReporte|null $tipoReporte
 * @property-read \App\Models\Ubicacion|null $ubicacion
 * @property-read \App\Models\Zona|null $zona
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereCategoriaReporteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereEstadoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereHora($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereImagenes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereLocalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte wherePrioridadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereSubidopor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereTipoReporteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereUbicacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reporte whereZonaId($value)
 * @mixin \Eloquent
 */
class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'subidopor',
        'fecha',
        'hora',
        'descripcion',
        'imagenes',
        'categoria_reporte_id',
        'tipo_reporte_id',
        'zona_id',
        'ubicacion_id',
        'prioridad_id',
        'estado_id',
        'local_id',
    ];

    protected $casts = [
        'imagenes' => 'array',
        'fecha' => 'date',
        'hora' => 'datetime:H:i:s',
    ];

    protected static function booted(): void
    {
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha ??= now()->toDateString();
            $reporte->hora ??= now()->format('H:i:s');

            // Establecer estado "Pendiente" si no se definió
            if (is_null($reporte->estado_id)) {
                $reporte->estado_id = \App\Models\Estado::where('descripcion', 'Pendiente')->value('id');
            }
        });

        // 🔹 Cuando se crea un nuevo reporte → agregar registro a la bitácora
        static::created(function ($reporte) {
            \App\Models\BitacoraEstado::create([
                'reporte_id'    => $reporte->id,
                'estado_id'     => $reporte->estado_id,
                'descripcion'   => $reporte->descripcion,
                'registrado_por'=> Auth::user()->name ?? 'Sistema',
                'fecha'         => now()->toDateString(),
                'hora'          => now()->format('H:i:s'),
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */
    public function categoria(): BelongsTo      { return $this->belongsTo(CategoriaReporte::class, 'categoria_reporte_id'); }
    public function tipoReporte(): BelongsTo    { return $this->belongsTo(TipoReporte::class, 'tipo_reporte_id'); }
    public function zona(): BelongsTo           { return $this->belongsTo(Zona::class, 'zona_id'); }
    public function ubicacion(): BelongsTo      { return $this->belongsTo(Ubicacion::class, 'ubicacion_id'); }
    public function prioridad(): BelongsTo      { return $this->belongsTo(Prioridad::class, 'prioridad_id'); }
    public function estado(): BelongsTo         { return $this->belongsTo(Estado::class, 'estado_id'); }
    public function local(): BelongsTo          { return $this->belongsTo(Local::class, 'local_id'); }
    public function bitacoras(): HasMany        { return $this->hasMany(BitacoraEstado::class); }
}
