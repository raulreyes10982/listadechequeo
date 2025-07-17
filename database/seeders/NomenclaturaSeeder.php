<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nomenclatura;

class NomenclaturaSeeder extends Seeder
{
    public function run(): void
    {
        $nomenclaturas = [
            ['codigo' => 'A-101', 'piso' => 1, 'modulo' => 'Módulo A'],
            ['codigo' => 'B-202', 'piso' => 2, 'modulo' => 'Módulo B'],
            ['codigo' => 'C-303', 'piso' => 3, 'modulo' => 'Módulo C'],
        ];

        foreach ($nomenclaturas as $item) {
            Nomenclatura::create($item);
        }
    }
}
