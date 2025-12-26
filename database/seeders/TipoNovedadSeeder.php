<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoNovedad;

class TipoNovedadSeeder extends Seeder
{
    public function run(): void
    {
        $novedades = [
            'Daño de ascensor',
            'Falla en escalera eléctrica',
            'Corte de energía',
            'Inundación o fuga de agua',
            'Falla en aire acondicionado',
            'Alarma activada sin causa',
            'Robo o intento de hurto',
            'Pérdida de objetos',
            'Persona sospechosa',
            'Accidente dentro del centro comercial',
            'Daño en cámaras de seguridad',
            'Ingreso no autorizado',
            'Daño en luminarias',
            'Problemas con proveedor externo',
            'Reporte de ruido excesivo',
            'Falla en señalización digital',
            'Emergencia médica',
            'Queja de cliente',
            'Derrame de líquidos',
            'Incidente con mascota o animal',
        ];

        foreach ($novedades as $novedad) {
            TipoNovedad::create([
                'descripcion' => $novedad,
            ]);
        }
    }
}
