<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoCambiarEstadoSeeder extends Seeder
{
    /**
     * Crea el permiso "cambiar_estado" y lo asigna a los roles que elijas.
     *
     * CÓMO EJECUTAR:
     *   php artisan db:seed --class=PermisoCambiarEstadoSeeder
     *
     * CÓMO ASIGNAR EL PERMISO A UN USUARIO DESDE FILAMENT:
     *   Ve a Gestión de Usuarios → Usuarios → Editar usuario
     *   El panel de Spatie mostrará el permiso "cambiar_estado" disponible.
     *
     * CÓMO ASIGNAR MANUALMENTE EN CÓDIGO:
     *   $user->givePermissionTo('cambiar_estado');
     *   $user->revokePermissionTo('cambiar_estado');
     */
    public function run(): void
    {
        // Limpiar caché de permisos para que Spatie los detecte frescos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Crear el permiso si no existe ────────────────────────────────
        $permiso = Permission::firstOrCreate(
            ['name' => 'cambiar_estado', 'guard_name' => 'web']
        );

        $this->command->info('✅ Permiso "cambiar_estado" creado/verificado.');

        // ── Asignar automáticamente a los roles que quieras ──────────────
        // Edita este array con los roles que deben tener el permiso por defecto.
        // Si un rol no existe todavía, se omite sin error.
        $rolesConPermiso = [
            'super_admin',
            'administrador',
            // 'supervisor',   // descomenta si quieres que supervisores también puedan
        ];

        foreach ($rolesConPermiso as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->where('guard_name', 'web')->first();

            if ($rol) {
                $rol->givePermissionTo($permiso);
                $this->command->info("  → Permiso asignado al rol: {$nombreRol}");
            } else {
                $this->command->warn("  ⚠ Rol '{$nombreRol}' no encontrado — omitido.");
            }
        }

        $this->command->info('');
        $this->command->info('Para asignar el permiso a un usuario individual:');
        $this->command->info('  $user->givePermissionTo("cambiar_estado");');
    }
}
