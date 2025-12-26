<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoCivil;

class EstadoCivilSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            'Soltero(a)',
            'Casado(a)',
            'Unión libre',
            'Separado(a) de hecho',
            'Separado(a) judicialmente',
            'Divorciado(a)',
            'Viudo(a)',
        ];

        foreach ($estados as $estado) {
            EstadoCivil::create([
                'descripcion' => $estado,
            ]);
        }
    }
}