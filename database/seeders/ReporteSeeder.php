<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReporteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reportes')->insert([
            [
                'subidopor' => 'admin',
                'fecha' => Carbon::now()->toDateString(),
                'hora' => Carbon::now()->toTimeString(),
                'descripcion' => 'Reporte por fuga de agua en zona común.',
                'imagenes' => json_encode(['imagen1.jpg', 'imagen2.jpg']),
                'categoria_reporte_id' => 1,
                'tipo_reporte_id' => 1,
                'zona_id' => 1,
                'prioridad_id' => 1,
                'estado_id' => 1,
                'local_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'subidopor' => 'juanperez',
                'fecha' => Carbon::now()->toDateString(),
                'hora' => Carbon::now()->toTimeString(),
                'descripcion' => 'Corte de energía en bloque B.',
                'imagenes' => json_encode(['foto_corte.jpg']),
                'categoria_reporte_id' => 2,
                'tipo_reporte_id' => 2,
                'zona_id' => 2,
                'prioridad_id' => 2,
                'estado_id' => 1,
                'local_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
