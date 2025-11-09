<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genero;

class GeneroSeeder extends Seeder
{
    public function run(): void
    {
        $generos = [
            'Masculino',
            'Femenino',
            'No binario',
            'Intersexual',
            'Transgénero',
            'Cisgénero',
            'Otro',
            'Prefiere no decirlo',
        ];

        foreach ($generos as $genero) {
            Genero::create([
                'descripcion' => $genero,
            ]);
        }
    }
}
