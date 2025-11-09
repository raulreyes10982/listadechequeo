<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
