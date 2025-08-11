<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BitacoraEstado extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'reporte_id',
        'estado_id',
        'descripcion',
        'registrado_por',
        'fecha',
        'hora',

    ];

    protected static function booted()
    {
        static::creating(function ($hist) {
            $hist->cambiado_por ??= Auth::user()->name ?? 'Sistema';
            $hist->fecha        ??= Carbon::now()->toDateString();
            $hist->hora         ??= Carbon::now()->format('H:i:s');
        });
    }


    public function reporte()    {return $this->belongsTo(Reporte::class);}
    public function estado()     {return $this->belongsTo(Estado::class);}


}

