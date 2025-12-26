<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;
use App\Models\Cargo;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        $cargosPorArea = [
            'Contabilidad' => [
                'Contador General',
                'Auxiliar Contable',
                'Tesorero',
            ],
            'Recursos Humanos' => [
                'Jefe de Talento Humano',
                'Analista de Nómina',
                'Asistente de Selección',
            ],
            'Dirección General' => [
                'Gerente General',
                'Asistente Ejecutivo',
                'Coordinador Administrativo',
            ],
            'Vigilancia Interna' => [
                'Jefe de Seguridad',
                'Vigilante',
                'Supervisor de Turno',
            ],
            'Control de Accesos' => [
                'Operador de Acceso',
                'Vigilante de Entrada',
                'Inspector de Equipaje',
            ],
            'Monitoreo CCTV' => [
                'Operador de Cámaras',
                'Analista de Monitoreo',
                'Encargado de Seguridad Electrónica',
            ],
            'Aseo y Limpieza' => [
                'Jefe de Aseo',
                'Operario de Limpieza',
                'Supervisor de Limpieza',
            ],
            'Mantenimiento' => [
                'Técnico Electricista',
                'Plomero',
                'Jefe de Mantenimiento',
            ],
            'Jardinería' => [
                'Jardinero',
                'Encargado de Zonas Verdes',
                'Supervisor de Jardinería',
            ],
        ];

        foreach ($cargosPorArea as $areaNombre => $cargos) {
            $area = Area::where('descripcion', $areaNombre)->first();

            if ($area) {
                foreach ($cargos as $cargoNombre) {
                    Cargo::create([
                        'descripcion' => $cargoNombre,
                        'area_id' => $area->id,
                    ]);
                }
            }
        }
    }
}
