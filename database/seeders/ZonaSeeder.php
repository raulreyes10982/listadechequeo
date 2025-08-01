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
        $zonas = ['Pasillo 1', 'Pasillo 2', 'Pasillo 3', 'Parqueadero', 'Local', 'baños 2 piso'];

    foreach ($zonas as $zona) {
        Zona::create(['descripcion' => $zona]);
    }
    }
}
