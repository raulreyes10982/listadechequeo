<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prioridad;

class PrioridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prioridads = ['Alta', 'Media', 'Baja'];

        foreach ($prioridads as $p) {
            Prioridad::create(['descripcion' => $p]);
        }
    }
}
