<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Orden de ejecución de Seeders
    |--------------------------------------------------------------------------
    | php artisan db:seed          → corre todos
    | php artisan migrate --seed   → migra + corre todos
    |
    | El orden importa: los catálogos primero, luego las relaciones
    |--------------------------------------------------------------------------
    */
    public function run(): void
    {
        $this->call([
            // ── Catálogos base ───────────────────────────────────────────
            DepartamentoSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            GeneroSeeder::class,
            TipoDocumentoSeeder::class,
            TipoContratoSeeder::class,
            EstadoCivilSeeder::class,
            GrupoSanguineoSeeder::class,

            // ── Colaboradores ────────────────────────────────────────────
            ColaboradorSeeder::class,

            // ── Permisos personalizados (Shield custom) ──────────────────
            // ✅ Siempre al final — necesita que los roles ya existan
            // Los roles los crea Shield con: php artisan shield:generate --all
            PermisosCustomSeeder::class,
        ]);
    }
}
