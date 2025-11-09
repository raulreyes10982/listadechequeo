<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Novedad;
use App\Models\TipoNovedad;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NovedadSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos todos los tipos de novedades existentes
        $tipos = TipoNovedad::pluck('id')->toArray();

        // Si no hay tipos de novedades, no se puede continuar
        if (empty($tipos)) {
            $this->command->warn('No hay tipos de novedades disponibles. Ejecuta primero el seeder de TipoNovedad.');
            return;
        }

        $novedades = [
            'Corte de energía en la zona norte del centro comercial.',
            'Fuga de agua detectada en el baño del segundo piso.',
            'Ascensor panorámico detenido por mantenimiento.',
            'Pérdida de objeto reportada por cliente.',
            'Colisión menor en parqueadero nivel 1.',
            'Personal de aseo reportó daño en lavamanos.',
            'Alarma activada por error en acceso peatonal 3.',
            'Cliente solicita asistencia en la tienda 102.',
            'Puesto de seguridad 4 sin reporte por 20 minutos.',
            'Ingreso de proveedor no autorizado detectado.',
        ];

        foreach ($novedades as $desc) {
            Novedad::create([
                'descripcion' => $desc,
                'subidopor' => 'admin@centrocomercial.com',
                'tipo_novedad_id' => fake()->randomElement($tipos),
                'fecha' => Carbon::now()->format('Y-m-d'),
                'hora' => Carbon::now()->format('H:i:s'),
            ]);
        }
    }
}

