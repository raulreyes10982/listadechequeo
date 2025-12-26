<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoEquipo;

class TipoEquipoSeeder extends Seeder
{
    public function run(): void
    {
        $equipos = [
            'Ascensor',
            'Escalera eléctrica',
            'Planta eléctrica',
            'Aire acondicionado',
            'Sistema de ventilación',
            'Sistema contra incendios',
            'Cámaras de seguridad (CCTV)',
            'Sistema de alarmas',
            'Control de acceso',
            'Paneles solares',
            'Bombas hidráulicas',
            'Generadores eléctricos',
            'Torres de enfriamiento',
            'Equipos de limpieza industrial',
            'Cuartos fríos / Neveras industriales',
            'Estaciones de carga para autos eléctricos',
            'Monitores de señalización digital (pantallas LED)',
        ];

        foreach ($equipos as $equipo) {
            TipoEquipo::create([
                'descripcion' => $equipo,
            ]);
        }
    }
}
