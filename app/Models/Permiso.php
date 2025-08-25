<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Permiso extends Model
{
    use HasFactory;

  protected $fillable = [
        'subidopor',
        'fecha_inicio_trabajo',
        'fecha_fin_trabajo', 
        'descripcion',
        'archivo_pdf',
        'local_id',
        'tipo_permiso_id',
        'tipo_actividad',
        'contratistas_id',
        'actividad',
        'colaborador_id',
    ];

    protected $casts = [
        'tipo_actividad' => 'array',
        // otros casts...
    ];

    protected $dates = ['fecha_inicio_trabajo', 'fecha_fin_trabajo'];

    protected static function booted(): void
    {
        static::creating(function ($reporte) {
            $reporte->subidopor = Auth::user()->name ?? 'Sistema';
            $reporte->fecha_inicio_trabajo ??= now()->toDateString();
            $reporte->fecha_fin_trabajo ??= now()->toDateString();
        });

    }
    
    // Relación con local
    public function local()
    {
        return $this->belongsTo(Local::class);
    }

    // Relación con tipo de permiso
    public function tipoPermiso()
    {
        return $this->belongsTo(TipoPermiso::class);
    }

    //Relación inversa con Contratistas.    
    public function contratistas()
    {
        return $this->belongsTo(Contratistas::class, 'contratistas_id');
    }

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class, 'colaborador_id');
    }

    public function trabajadores()
    {
        return $this->hasMany(\App\Models\Trabajador::class);
    }

    // Calcular días restantes del permiso
    public function getDiasRestantesAttribute()
    {
        $fechaFinal = Carbon::parse($this->fecha_fin_trabajo); // Fecha de finalización del trabajo
        $fechaInicio = Carbon::parse($this->fecha_inicio_trabajo); // Fecha de inicio del trabajo
        $hoy = Carbon::now();

        // Si la fecha de inicio y la fecha final son la misma, devolver 1
        if ($fechaInicio->equalTo($fechaFinal)) {
            return 1;
        }

        // Comparar las fechas para determinar si la fecha final es mayor o menor que hoy
        if ($fechaFinal->greaterThan($hoy)) {
            // Si la fecha final es mayor que hoy (futura), devuelve días positivos
            return $hoy->diffInDays($fechaFinal);
        } else {
            // Si la fecha final es menor o igual a hoy (pasada), devuelve días negativos
            return -$fechaFinal->diffInDays($hoy);
        }
    }

}
