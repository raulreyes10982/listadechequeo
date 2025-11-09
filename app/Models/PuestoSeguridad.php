<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PuestoSeguridad extends Model
{
    protected $fillable = [
        'codigo',
        'puesto',
        'inicio_hora',
        'fin_hora',
        'descripcion',
        'qr_token',
        'qr_expira',
    ];

    protected $casts = [
        'qr_expira' => 'date',
        'inicio_hora' => 'datetime',
        'fin_hora' => 'datetime',
    ];

    public function generarQrSiNecesario()
    {
        $expira = $this->qr_expira ? Carbon::parse($this->qr_expira) : null;
        
        if (!$this->qr_token || !$expira || Carbon::now()->greaterThan($expira)) {
            $this->qr_token = Str::uuid();
            $this->qr_expira = Carbon::now()->addDays(30);
            $this->save();
        }
        return $this;
    }

    public function getQrContentAttribute()
    {
        return json_encode([
            'id' => $this->id,
            'codigo' => $this->codigo,
            'puesto' => $this->puesto,
            'inicio_hora' => $this->inicio_hora,
            'fin_hora' => $this->fin_hora,
            'token' => $this->qr_token,
            'expira' => $this->qr_expira
        ]);
    }
}