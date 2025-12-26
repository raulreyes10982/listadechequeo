<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaLocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['descripcion' => 'Local'],
            ['descripcion' => 'Oficinas'],
            ['descripcion' => 'Burbuja'],
            ['descripcion' => 'Stand comercial'],
        ];

        DB::table('categoria_locals')->insert($categorias);
    }
}
