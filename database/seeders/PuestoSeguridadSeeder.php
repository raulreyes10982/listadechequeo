<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PuestoSeguridad;

class PuestoSeguridadSeeder extends Seeder
{
    public function run(): void
    {
        $puestos = [
            [
                'codigo' => 'APOLO 1',
                'puesto' => 'Entrada peatonal 1',
                'inicio_hora' => '10:00:00',
                'fin_hora' => '22:00:00',
                'descripcion' => 'Turno de 12 horas',
            ],
            [
                'codigo' => 'APOLO 2',
                'puesto' => 'Entrada vehicular',
                'inicio_hora' => '06:00:00',
                'fin_hora' => '18:00:00',
                'descripcion' => 'Turno diurno',
            ],
            [
                'codigo' => 'APOLO 3',
                'puesto' => 'Piso 1 zona de comidas',
                'inicio_hora' => '14:00:00',
                'fin_hora' => '22:00:00',
                'descripcion' => 'Turno tarde',
            ],
            [
                'codigo' => 'APOLO 4',
                'puesto' => 'Parqueadero subterráneo',
                'inicio_hora' => '18:00:00',
                'fin_hora' => '06:00:00',
                'descripcion' => 'Turno nocturno',
            ],
            [
                'codigo' => 'APOLO 5',
                'puesto' => 'Recepción administrativa',
                'inicio_hora' => '08:00:00',
                'fin_hora' => '17:00:00',
                'descripcion' => 'Oficina central',
            ],
        ];

        foreach ($puestos as $puesto) {
            PuestoSeguridad::create($puesto);
        }
    }
}
