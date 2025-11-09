<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Novedad extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'hora',
        'descripcion',
        'subidopor',
        'tipo_novedad_id',
    ];

    public function tipoNovedad()
    {
        return $this->belongsTo(TipoNovedad::class);
    }

    protected static function booted()
{
    static::creating(function ($novedad) {
        if (Auth::check()) {
            $novedad->subidopor = Auth::user()->name;
        }
    }); 

}
}