<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areasPorDepartamento = [
            'Administración' => [
                'Contabilidad',
                'Recursos Humanos',
                'Dirección General',
            ],
            'Seguridad' => [
                'Vigilancia Interna',
                'Control de Accesos',
                'Monitoreo CCTV',
            ],
            'Servicios Generales' => [
                'Aseo y Limpieza',
                'Mantenimiento',
                'Jardinería',
            ],
        ];

        foreach ($areasPorDepartamento as $nombreDepartamento => $areas) {
            $departamento = Departamento::where('descripcion', $nombreDepartamento)->first();

            if ($departamento) {
                foreach ($areas as $areaNombre) {
                    Area::create([
                        'descripcion' => $areaNombre,
                        'departamento_id' => $departamento->id,
                    ]);
                }
            }
        }
    }
}
