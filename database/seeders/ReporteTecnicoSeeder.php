<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ReporteTecnico;
use App\Models\Equipo;
use App\Models\TipoIntervencion;

class ReporteTecnicoSeeder extends Seeder
{
    public function run(): void
    {
        $equipoId = Equipo::first()?->id ?? 1;
        $intervencionId = TipoIntervencion::first()?->id ?? 1;

        ReporteTecnico::create([
            'fecha' => Carbon::now()->toDateString(),
            'hora' => Carbon::now()->format('H:i:s'),
            'descripcion' => 'Reporte técnico de prueba',
            'equipo_id' => $equipoId,
            'tipo_intervencion_id' => $intervencionId,
            'subidopor' => 'admin',
        ]);
    }
}
