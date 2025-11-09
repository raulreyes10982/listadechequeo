<?php

namespace Database\Seeders;

use App\Models\Ubicacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UbicacionSeeder extends Seeder
{
    
    public function run(): void
    {
        $ubicacions = ['Pasillo 1', 'Pasillo 2', 'Pasillo 3', 'Parqueadero zona naranja', 'Parqueadero zona azul', 'baños 2 piso'];

        foreach ($ubicacions as $ubicacion) 
        {
            Ubicacion::create(['descripcion' => $ubicacion]);
        }

    }
}
