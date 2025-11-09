<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $estados = ['Pendiente', 'En proceso', 'verificado', 'Finalizado'];

    foreach ($estados as $estado) {
        Estado::create(['descripcion' => $estado]);
    }
    }
}
