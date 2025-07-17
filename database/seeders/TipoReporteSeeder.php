<?php

namespace Database\Seeders;

use App\Models\TipoReporte;
use Illuminate\Database\Seeder;

class TipoReporteSeeder extends Seeder
{
    public function run(): void
    {
        TipoReporte::insert([
            ['descripcion' => 'Falla de energía', 'categoria_reporte_id' => 3],
            ['descripcion' => 'Robo menor', 'categoria_reporte_id' => 1],
            ['descripcion' => 'Incendio', 'categoria_reporte_id' => 2],
        ]);
    }
}
