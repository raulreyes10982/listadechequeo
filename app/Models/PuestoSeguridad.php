<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PuestoSeguridad extends Model
{
    use HasFactory;

    protected $table = 'puesto_seguridads';
    
    protected $fillable = [
        'codigo',
        'puesto',
        'inicio_hora',
        'fin_hora',
        'descripcion',
        'qr_token',
        'qr_expira',
        'qr_generado_en'
    ];

    protected $casts = [
        'qr_expira' => 'datetime',
        'qr_generado_en' => 'datetime',
        'inicio_hora' => 'datetime:H:i',
        'fin_hora' => 'datetime:H:i',
    ];

    /**
     * Generar QR seguro si es necesario
     */
    public function generarQrSiNecesario(): void
    {
        $now = Carbon::now();
        
        // 🔐 CORRECCIÓN CRÍTICA: Token único y criptográficamente seguro
        if (!$this->qr_token || !$this->qr_expira || $this->qr_expira->isPast()) {
            // Usamos hash SHA256 de UUID + app key + timestamp
            $this->qr_token = hash('sha256', 
                Str::uuid() . config('app.key') . microtime(true)
            );
            $this->qr_expira = $now->copy()->addDays(30);
            $this->qr_generado_en = $now;
            $this->save();
        }
    }

    /**
     * Verificar token QR de manera segura (ataques timing)
     */
    public function validarTokenQr(string $tokenProporcionado): bool
    {
        if (!$this->qr_token || !$this->qr_expira) {
            return false;
        }
        
        // 🔐 CORRECCIÓN: hash_equals previene ataques de timing
        $sonIguales = hash_equals($this->qr_token, $tokenProporcionado);
        $noExpirado = $this->qr_expira->isFuture();
        
        return $sonIguales && $noExpirado;
    }

    /**
     * Contenido del QR para generar código
     */
    public function getQrContentAttribute(): string
    {
        return json_encode([
            'codigo' => $this->codigo,
            'token' => $this->qr_token,
            'puesto' => $this->puesto,
            'expira' => $this->qr_expira?->toIso8601String()
        ]);
    }

    /**
     * RELACIONES CORREGIDAS
     */
    public function turnos()
    {
        return $this->hasMany(RegistrarTurno::class, 'puesto_seguridad_id');
    }

    public function verificaciones()
    {
        return $this->hasManyThrough(
            VerificacionTurno::class,
            RegistrarTurno::class,
            'puesto_seguridad_id',
            'registrar_turno_id',
            'id',
            'id'
        );
    }

    /**
     * SCOPE: Puestos activos (con turno actual)
     */
    public function scopeActivos($query)
    {
        return $query->whereHas('turnos', function($q) {
            $q->whereDate('fecha', Carbon::today())
                ->whereTime('hora_inicio', '<=', Carbon::now()->toTimeString())
                ->whereTime('hora_fin', '>=', Carbon::now()->toTimeString());
        });
    }

    /**
     * SCOPE: Puestos que necesitan QR
     */
    public function scopeNecesitaQr($query)
    {
        return $query->whereNull('qr_token')
                    ->orWhere('qr_expira', '<=', Carbon::now());
    }
}