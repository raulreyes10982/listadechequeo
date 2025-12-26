<?php

namespace Database\Seeders;

use App\Models\CategoriaReporte;
use Illuminate\Database\Seeder;

class CategoriaReporteSeeder extends Seeder
{
    public function run(): void
    {
        CategoriaReporte::insert([
            ['descripcion' => 'Seguridad'],
            ['descripcion' => 'Emergencia'],
            ['descripcion' => 'Técnico'],
            ['descripcion' => 'Administrativo'],
        ]);
    }
}