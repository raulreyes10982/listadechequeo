<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Control de acceso — usuario activo o bloqueado
            $table->boolean('is_active')
                ->default(true)
                ->after('remember_token')
                ->comment('false = usuario bloqueado, no puede iniciar sesión');

            // ✅ Contraseña temporal — obliga al usuario a cambiarla al primer ingreso
            $table->boolean('must_change_password')
                ->default(false)
                ->after('is_active')
                ->comment('true = debe cambiar contraseña antes de usar el sistema');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'must_change_password']);
        });
    }
};
