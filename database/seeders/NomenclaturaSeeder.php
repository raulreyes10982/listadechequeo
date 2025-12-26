<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nomenclatura;

class NomenclaturaSeeder extends Seeder
{
    public function run(): void
    {
        $nomenclaturas = [];

        // Piso 1 → categoria_local_id = 1, del 01 al 73
        for ($i = 1; $i <= 73; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 1,
                'codigo' => '1-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 1,
                'modulo' => 'Módulo A',
            ];
        }

        // Piso 2 → categoria_local_id = 2, del 01 al 49
        for ($i = 1; $i <= 49; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 1,
                'codigo' => '2-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 2,
                'modulo' => 'Módulo A',
            ];
        }

        // Piso 3 → categoria_local_id = 3, del 01 al 70
        for ($i = 1; $i <= 70; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 1,
                'codigo' => '3-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 3,
                'modulo' => 'Módulo A',
            ];
        }

        for ($i = 1; $i <= 30; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 2,
                'codigo' => '2-1' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 2,
                'modulo' => 'Módulo A',
            ];
        }

        for ($i = 1; $i <= 30; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 2,
                'codigo' => '3-1' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 3,
                'modulo' => 'Módulo A',
            ];
        }

        for ($i = 1; $i <= 30; $i++) {
            $nomenclaturas[] = [
                'categoria_local_id' => 2,
                'codigo' => '4-1' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'piso' => 3,
                'modulo' => 'Módulo A',
            ];
        }

        // Inserción masiva
        Nomenclatura::insert($nomenclaturas);
    }
}
