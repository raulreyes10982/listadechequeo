<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReporteTecnico;
use Carbon\Carbon;

class ReporteTecnicoSeeder extends Seeder
{
    public function run(): void
    {
        $reportes = [
            [
                'fecha' => '2025-07-01',
                'hora' => '08:00:00',
                'descripcion' => 'Revisión de fuga de agua en el cuarto de bombas.',
                'equipo_id' => 1,
                'tipo_intervencion_id' => 5, // Inspección
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-02',
                'hora' => '10:30:00',
                'descripcion' => 'Mantenimiento preventivo del generador de emergencia.',
                'equipo_id' => 2,
                'tipo_intervencion_id' => 3, // Mantenimiento preventivo
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-03',
                'hora' => '14:00:00',
                'descripcion' => 'Daño en luminaria del parqueadero nivel 2.',
                'equipo_id' => 3,
                'tipo_intervencion_id' => 2, // Daño
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-04',
                'hora' => '09:15:00',
                'descripcion' => 'Cambio de filtros del sistema HVAC.',
                'equipo_id' => 4,
                'tipo_intervencion_id' => 3,
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-05',
                'hora' => '11:45:00',
                'descripcion' => 'Reparación de puerta automática en entrada principal.',
                'equipo_id' => 5,
                'tipo_intervencion_id' => 4, // Correctivo
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-06',
                'hora' => '15:20:00',
                'descripcion' => 'Verificación de alarma de incendio nivel 3.',
                'equipo_id' => 6,
                'tipo_intervencion_id' => 5,
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-07',
                'hora' => '13:40:00',
                'descripcion' => 'Falla en la bomba hidráulica del sistema de riego.',
                'equipo_id' => 7,
                'tipo_intervencion_id' => 1, // Falla
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-08',
                'hora' => '07:50:00',
                'descripcion' => 'Revisión de ventiladores del cuarto de servidores.',
                'equipo_id' => 8,
                'tipo_intervencion_id' => 5,
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-09',
                'hora' => '10:00:00',
                'descripcion' => 'Reparación de interruptor dañado en sala de juntas.',
                'equipo_id' => 9,
                'tipo_intervencion_id' => 4,
                'subidopor' => 'Sistema',
            ],
            [
                'fecha' => '2025-07-10',
                'hora' => '16:10:00',
                'descripcion' => 'Cierre del caso por mantenimiento en zona de carga.',
                'equipo_id' => 10,
                'tipo_intervencion_id' => 4,
                'subidopor' => 'Sistema',
            ],
        ];

        foreach ($reportes as $reporte) {
            ReporteTecnico::create($reporte);
        }
    }
}
