<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosCustomSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Permisos personalizados del sistema
    |--------------------------------------------------------------------------
    | Este seeder crea los permisos custom de Filament Shield y los asigna
    | automáticamente al rol super_admin.
    |
    | Ejecutar: php artisan db:seed --class=PermisosCustomSeeder
    |
    | Es seguro correrlo múltiples veces — usa firstOrCreate para no duplicar.
    |--------------------------------------------------------------------------
    */

    // ✅ Agrega aquí nuevos permisos custom cuando los necesites
    private const PERMISOS = [
        'cambiar_estado',
        // 'otro_permiso',
        // 'permiso_especial',
    ];

    // ✅ Roles que reciben TODOS los permisos custom automáticamente
    private const ROLES_ADMIN = [
        'super_admin',
        //'administrador',
    ];

    public function run(): void
    {
        // Limpiar caché de Spatie antes de empezar
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('📋 Creando permisos personalizados...');

        $permisosCreados = collect();

        foreach (self::PERMISOS as $nombre) {
            $permiso = Permission::firstOrCreate([
                'name'       => $nombre,
                'guard_name' => 'web',
            ]);

            $permisosCreados->push($permiso);

            $this->command->line("  " . ($permiso->wasRecentlyCreated ? '✅ Creado' : '⏭  Ya existía') . ": {$nombre}");
        }

        $this->command->info('');
        $this->command->info('👑 Asignando permisos a roles administradores...');

        foreach (self::ROLES_ADMIN as $nombreRol) {
            $rol = Role::where('name', $nombreRol)->where('guard_name', 'web')->first();

            if (! $rol) {
                $this->command->warn("  ⚠️  Rol no encontrado: {$nombreRol} (omitido)");
                continue;
            }

            // syncPermissions agrega sin duplicar
            $rol->givePermissionTo($permisosCreados);

            $this->command->line("  ✅ Asignados al rol: {$nombreRol}");
        }

        // Limpiar caché al final para que los cambios apliquen de inmediato
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('✅ PermisosCustomSeeder completado.');
        $this->command->info('   Recuerda asignar los permisos a otros roles desde Shield → Roles.');
    }
}
