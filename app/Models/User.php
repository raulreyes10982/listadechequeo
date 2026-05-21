<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function colaborador(): HasOne
    {
        return $this->hasOne(Colaborador::class);
    }

    /**
     * Token de API para la app móvil (Sanctum).
     */
    public function crearTokenAppMovil(): string
    {
        $this->tokens()->where('name', 'app-movil')->delete();

        return $this->createToken('app-movil')->plainTextToken;
    }

    /**
     * Resuelve el colaborador vinculado por user_id o correo.
     */
    public function resolverColaborador(): ?Colaborador
    {
        if ($this->colaborador) {
            return $this->colaborador;
        }

        return Colaborador::query()
            ->where(function ($query) {
                $query->where('user_id', $this->id)
                    ->orWhere('correo_corporativo', $this->email)
                    ->orWhere('correo_personal', $this->email);
            })
            ->first();
    }
}
