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
            ['nombre' => 'Studio F', 'nomenclatura_id' => $nomenclaturaIds['1-01'] ?? null],
            ['nombre' => 'Tigo', 'nomenclatura_id' => $nomenclaturaIds['1-03'] ?? null],
            ['nombre' => 'Offcorss', 'nomenclatura_id' => $nomenclaturaIds['1-06'] ?? null],
            ['nombre' => 'Koaj', 'nomenclatura_id' => $nomenclaturaIds['1-10'] ?? null],
            ['nombre' => 'Yoyo', 'nomenclatura_id' => $nomenclaturaIds['1-13'] ?? null],
            ['nombre' => 'Quest', 'nomenclatura_id' => $nomenclaturaIds['1-14'] ?? null],
            ['nombre' => 'Totto', 'nomenclatura_id' => $nomenclaturaIds['1-16'] ?? null],
            ['nombre' => 'Verjel & Co', 'nomenclatura_id' => $nomenclaturaIds['4-103'] ?? null],
            ['nombre' => 'Coal Bussiness Connection', 'nomenclatura_id' => $nomenclaturaIds['4-109'] ?? null],
            ['nombre' => 'Ci Carboex', 'nomenclatura_id' => $nomenclaturaIds['4-110'] ?? null],
            ['nombre' => 'Focus Group Consultores', 'nomenclatura_id' => $nomenclaturaIds['4-111'] ?? null],
            ['nombre' => 'Colproyectos', 'nomenclatura_id' => $nomenclaturaIds['4-115'] ?? null],
            ['nombre' => 'Sociedad Inversiones y distribuciones Dar', 'nomenclatura_id' => $nomenclaturaIds['4-117'] ?? null],
            ['nombre' => 'Fundacion Alianza Biocuenca', 'nomenclatura_id' => $nomenclaturaIds['4-118'] ?? null],
            ['nombre' => 'Exporiente Representaciones ltda', 'nomenclatura_id' => $nomenclaturaIds['4-120'] ?? null],
            ['nombre' => 'Onix Cosultores', 'nomenclatura_id' => $nomenclaturaIds['4-125'] ?? null],
        ];

        foreach ($locals as $item) {
            if ($item['nomenclatura_id']) {
                Local::create($item);
            }
        }
    }
}
