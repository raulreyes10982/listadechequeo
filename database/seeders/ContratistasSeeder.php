<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contratistas;

class ContratistasSeeder extends Seeder
{
    public function run(): void
    {
        $contratistas = [
            'Seguridad Atlas Ltda.',
            'Aseo y Limpieza S.A.S.',
            'Mantenimiento Eléctrico Andino',
            'Control de Plagas Zona Cero',
            'Mantenimiento de Ascensores Elitelift',
            'Jardinería y Áreas Verdes Naturaleza Viva',
            'Recolección de Residuos EcoCiclo',
            'Servicios Generales Global Services',
            'Instalaciones Hidrosanitarias AquaPro',
            'Soporte Técnico RedCom S.A.S.',
        ];

        foreach ($contratistas as $nombre) {
            Contratistas::create([
                'descripcion' => $nombre,
            ]);
        }
    }
}
