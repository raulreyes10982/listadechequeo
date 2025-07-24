<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 

class EstadoReporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estado_reportes')->insert([
            ['nombre' => 'Pendiente'],
            ['nombre' => 'En revisión'],
            ['nombre' => 'Asignado'],
            ['nombre' => 'En espera'],
            ['nombre' => 'Rechazado'],
            ['nombre' => 'Reabierto'],
            ['nombre' => 'En proceso'],
            ['nombre' => 'Finalizado'],
            ['nombre' => 'Cancelado'],
        ]);
    }
}
