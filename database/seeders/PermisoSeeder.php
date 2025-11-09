<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $fechaInicio = Carbon::now()->subDays(rand(0, 15));
            $fechaFin = (clone $fechaInicio)->addDays(rand(0, 3));

            DB::table('permisos')->insert([
                'subidopor' => 'Supervisor ' . $i,
                'fecha_inicio_trabajo' => $fechaInicio->toDateString(),
                'fecha_fin_trabajo' => $fechaFin->toDateString(),
                'descripcion' => 'Permiso generado para actividad de mantenimiento ' . $i,
                'actividad' => 'Mantenimiento de equipos eléctricos y revisión de seguridad ' . $i,
                'tipo_actividad' => json_encode(['Eléctrica', 'Mecánica']),
                'archivo_pdf' => 'permiso_' . $i . '.pdf',
                'local_id' => rand(1, 3),          // Ajusta según tus registros
                'contratistas_id' => rand(1, 3),   // Ajusta según tus registros
                'tipo_permiso_id' => rand(1, 3),   // Ajusta según tus registros
                'colaborador_id' => rand(1, 3),    // Ajusta según tus registros
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
