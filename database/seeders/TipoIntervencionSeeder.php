<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 


class TipoIntervencionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipo_intervencions')->insert([
            ['nombre' => 'Falla'],
            ['nombre' => 'Daño'],
            ['nombre' => 'Mantenimiento preventivo'],
            ['nombre' => 'Mantenimiento correctivo'],
            ['nombre' => 'Inspección'],
        ]);
    }
}
