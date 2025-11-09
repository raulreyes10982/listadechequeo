<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Zona;

class ZonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zonas = ['Zona Privada', ' Zona Comun', 'Zona Externa', 'Zona Parqueadero',];

    foreach ($zonas as $zona) {
        Zona::create(['descripcion' => $zona]);
    }
    }
}
