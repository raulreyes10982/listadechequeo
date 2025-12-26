<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            'Administración',
            'Seguridad',
            'Servicios Generales',
        ];

        foreach ($departamentos as $nombre) {
            Departamento::create([
                'descripcion' => $nombre,
            ]);
        }
    }
}
