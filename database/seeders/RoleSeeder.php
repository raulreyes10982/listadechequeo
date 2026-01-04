<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 🔐 PERMISOS BASE DEL SISTEMA
        $permissions = [
            // Dashboard
            'view_dashboard',
            
            // Puestos de Seguridad
            'view_puestos',
            'create_puestos', 
            'edit_puestos',
            'delete_puestos',
            'manage_puestos',
            'generate_qr',
            
            // Turnos
            'view_turnos',
            'create_turnos',
            'edit_turnos', 
            'delete_turnos',
            'manage_turnos',
            
            // Verificaciones QR
            'scan_qr',
            'view_verificaciones',
            'export_verificaciones',
            
            // Reportes
            'view_reportes',
            'export_reportes',
            'view_reportes_equipos',
            
            // Bitácora
            'view_bitacora',
            
            // Colaboradores
            'view_colaboradores',
            'manage_colaboradores',
            
            // Administración
            'manage_roles',
            'manage_usuarios',
        ];
        
        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // 👑 ROL: SUPER ADMINISTRADOR (acceso total)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());
        
        // 👔 ROL: ADMINISTRADOR
        $admin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view_dashboard',
            'manage_puestos',
            'manage_turnos',
            'view_verificaciones',
            'view_reportes',
            'export_reportes',
            'view_colaboradores',
            'manage_colaboradores',
        ]);
        
        // 👷 ROL: SUPERVISOR
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->givePermissionTo([
            'view_dashboard',
            'view_puestos',
            'create_turnos',
            'edit_turnos',
            'scan_qr',
            'view_verificaciones',
            'view_reportes_equipos',
            'view_colaboradores',
        ]);
        
        // 🛡️ ROL: GUARDIA
        $guardia = Role::firstOrCreate(['name' => 'guardia', 'guard_name' => 'web']);
        $guardia->givePermissionTo([
            'scan_qr',
            'view_turnos', // Solo sus turnos
        ]);
        
        // 📊 ROL: AUDITOR
        $auditor = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditor->givePermissionTo([
            'view_bitacora',
            'view_reportes',
            'export_reportes',
            'view_verificaciones',
        ]);
        
        // 🎯 ASIGNAR SUPER ADMIN AL USUARIO PRINCIPAL (opcional)
        // $user = User::where('email', 'admin@ejemplo.com')->first();
        // if ($user) {
        //     $user->assignRole('super_admin');
        // }
        
        $this->command->info('✅ Roles y permisos creados exitosamente!');
        $this->command->info('Roles disponibles: super_admin, administrador, supervisor, guardia, auditor');
    }
}