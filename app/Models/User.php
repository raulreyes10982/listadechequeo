<?php

namespace App\Models;

use App\Models\Colaborador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool hasPermissionTo(string|\Spatie\Permission\Models\Permission $permission, string|null $guardName = null)
 * @method bool hasAnyPermission(string|\Spatie\Permission\Models\Permission ...$permissions)
 * @method bool hasAllPermissions(string|\Spatie\Permission\Models\Permission ...$permissions)
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role $roles, string|null $guardName = null)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Models\Role ...$roles)
 * @method bool can(string $ability, mixed $arguments = [])
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /*
    |--------------------------------------------------------------------------
    | ✅ Constante centralizada de roles administradores
    | Usar SIEMPRE esta constante en lugar de arrays hardcodeados
    | Así si cambias un nombre de rol, solo lo cambias aquí
    |--------------------------------------------------------------------------
    */
    public const ROLES_ADMIN = ['super_admin', 'administrador'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'    => 'datetime',
        'password'             => 'hashed',
        'is_active'            => 'boolean',
        'must_change_password' => 'boolean',
    ];

    public function colaborador(): HasOne
    {
        return $this->hasOne(Colaborador::class, 'user_id');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function mustChangePassword(): bool
    {
        return $this->must_change_password === true;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    // ✅ Helper para verificar si es administrador — usa Spatie correctamente
    public function esAdmin(): bool
    {
        return $this->hasAnyRole(self::ROLES_ADMIN);
    }

    /*
    |--------------------------------------------------------------------------
    | Resolver colaborador vinculado al usuario
    |--------------------------------------------------------------------------
    | Busca por user_id primero (más rápido), luego por correo como fallback.
    | Usado por el escáner QR y el filtro de verificaciones de turnos.
    |--------------------------------------------------------------------------
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
