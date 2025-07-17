<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Local;
use App\Models\Nomenclatura;

class LocalSeeder extends Seeder
{
    public function run(): void
    {
        $nomenclaturaIds = Nomenclatura::pluck('id', 'codigo');

        $locals = [
            ['nombre' => 'Tienda 1', 'nomenclatura_id' => $nomenclaturaIds['A-101'] ?? null],
            ['nombre' => 'Panadería 2', 'nomenclatura_id' => $nomenclaturaIds['B-202'] ?? null],
            ['nombre' => 'Cafetería 3', 'nomenclatura_id' => $nomenclaturaIds['C-303'] ?? null],
        ];

        foreach ($locals as $item) {
            if ($item['nomenclatura_id']) {
                Local::create($item);
            }
        }
    }
}
