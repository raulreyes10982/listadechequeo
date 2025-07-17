<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipo;
use App\Models\TipoEquipo;

class EquipoSeeder extends Seeder
{
    public function run(): void
    {
        $equiposPorTipo = [
            'Ascensor' => [
                'Panorámico 1',
                'Panorámico 2',
                'Carga Nivel -1',
            ],
            'Escalera eléctrica' => [
                'Escalera eléctrica A1',
                'Escalera eléctrica B2',
                'Escalera eléctrica Central',
            ],
            'Planta eléctrica' => [
                'Planta Diésel 1',
                'Planta Diésel 2',
            ],
            'Sistema de ventilación' => [
                'Extractor Zona 1',
                'Ventilador Sótano',
            ],
        ];

        foreach ($equiposPorTipo as $tipo => $equipos) {
            $tipoEquipo = TipoEquipo::where('descripcion', $tipo)->first();

            if ($tipoEquipo) {
                foreach ($equipos as $equipoNombre) {
                    Equipo::create([
                        'descripcion' => $equipoNombre,
                        'tipo_equipo_id' => $tipoEquipo->id,
                    ]);
                }
            }
        }
    }
}
