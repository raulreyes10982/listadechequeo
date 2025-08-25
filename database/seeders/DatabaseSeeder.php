<?php

namespace Database\Seeders;

use App\Models\CategoriaLocal;
use App\Models\Prioridad;
use App\Models\TipoPermiso;
use App\Models\Ubicacion;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([

            
            GrupoSanguineoSeeder::class,
            EstadoCivilSeeder::class,
            GeneroSeeder::class,
            TipoDocumentoSeeder::class,
            TipoContratoSeeder::class,
            TipoEquipoSeeder::class,
            TipoNovedadSeeder::class,
            PuestoSeguridadSeeder::class,
            CategoriaLocalSeeder::class,
            NomenclaturaSeeder::class,
            LocalSeeder::class,
            DepartamentoSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            EquipoSeeder::class,
            ContratistasSeeder::class,
            NovedadSeeder::class,
            CategoriaReporteSeeder::class,
            TipoReporteSeeder::class,
            EstadoReporteSeeder::class,
            TipoIntervencionSeeder::class,
            ReporteTecnicoSeeder::class,
            HistorialEstadoReporteSeeder::class,
            ColaboradorSeeder::class,
            ZonaSeeder::class,
            UbicacionSeeder::class,
            PrioridadSeeder::class,
            EstadoSeeder::class,
            ReporteSeeder::class,
            TipoPermiso::class,
        ]);
    }
}
