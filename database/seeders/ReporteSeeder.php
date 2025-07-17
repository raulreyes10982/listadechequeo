<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reporte;
use App\Models\CategoriaReporte;
use App\Models\TipoReporte;

class ReporteSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs por nombre (para evitar hardcodear valores)
        $tecnico = CategoriaReporte::where('descripcion', 'Técnico')->first();
        $seguridad = CategoriaReporte::where('descripcion', 'Seguridad')->first();
        $emergencia = CategoriaReporte::where('descripcion', 'Emergencia')->first();

        $fallaEnergia = TipoReporte::where('descripcion', 'Falla de energía')->first();
        $roboMenor = TipoReporte::where('descripcion', 'Robo menor')->first();
        $incendio = TipoReporte::where('descripcion', 'Incendio')->first();

        // Crear reportes de ejemplo
        Reporte::insert([
            [
                'fecha' => now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'descripcion' => 'Se reportó un corte de energía en el nivel 1.',
                'imagenes' => json_encode(['falla_energia_1.jpg']),
                'subidopor' => 'admin',
                'categoria_reporte_id' => $tecnico->id ?? null,
                'tipo_reporte_id' => $fallaEnergia->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha' => now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'descripcion' => 'Un visitante reportó un robo menor en el parqueadero sur.',
                'imagenes' => json_encode(['robo_menor_1.jpg']),
                'subidopor' => 'supervisor_seguridad',
                'categoria_reporte_id' => $seguridad->id ?? null,
                'tipo_reporte_id' => $roboMenor->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha' => now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'descripcion' => 'Conato de incendio en local 104, controlado con extintores.',
                'imagenes' => json_encode(['incendio_1.jpg']),
                'subidopor' => 'jefe_emergencias',
                'categoria_reporte_id' => $emergencia->id ?? null,
                'tipo_reporte_id' => $incendio->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
