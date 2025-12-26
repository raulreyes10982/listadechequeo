<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GrupoSanguineo;

class GrupoSanguineoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = [
            'A POSITIVO (A+)',
            'A NEGATIVO (A-)',
            'B POSITIVO (B+)',
            'B NEGATIVO (B-)',
            'AB POSITIVO (AB+)',
            'AB NEGATIVO (AB-)',
            'O POSITIVO (O+)',
            'O NEGATIVO (O-)',
        ];
    
    foreach ($grupos as $grupo) {
            GrupoSanguineo::create([
                'descripcion' => $grupo,
            ]);
        }
    }
}
