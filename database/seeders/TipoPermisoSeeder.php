<?php

namespace Database\Seeders;

use App\Models\TipoPermiso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipopermisos = [
            'Permiso tercero',
            'Permiso interno',
            'Permiso externo',
        ];

        foreach ($tipopermisos as $tipopermiso) {
            TipoPermiso::create([
                'descripcion' => $tipopermiso,
            ]);
        }
    }
}
