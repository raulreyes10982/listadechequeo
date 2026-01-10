<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":[
            // Permisos existentes...
            "view_dashboard",
            
            // PERMISOS NUEVOS PARA CAMBIAR ESTADO
            "cambiar_estado_bitacora",
            "view_bitacora_estado",
            "create_bitacora_estado",
            "update_bitacora_estado",
            
            // Permisos existentes continúan...
            "view_puestos",
            "create_puestos",
            // ... resto de permisos
        ]}]';

        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    // ... resto del código
}
