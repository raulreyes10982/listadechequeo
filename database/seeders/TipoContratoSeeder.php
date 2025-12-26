<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoContrato;

class TipoContratoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Contrato a término fijo',
            'Contrato a término indefinido',
            'Contrato por obra o labor',
            'Contrato de aprendizaje',
            'Contrato de prestación de servicios',
            'Contrato ocasional, accidental o transitorio',
            'Contrato sindical',
            'Contrato a tiempo parcial',
            'Contrato en misión',
        ];

        foreach ($tipos as $tipo) {
            TipoContrato::create([
                'descripcion' => $tipo,
            ]);
        }
    }
}
