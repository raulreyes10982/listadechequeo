<?php

namespace Database\Seeders;

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
            NomenclaturaSeeder::class,
            LocalSeeder::class,
            GrupoSanguineoSeeder::class,
            EstadoCivilSeeder::class,
            GeneroSeeder::class,
            TipoDocumentoSeeder::class,
            TipoContratoSeeder::class,
            TipoEquipoSeeder::class,
            TipoNovedadSeeder::class,
            PuestoSeguridadSeeder::class,
            DepartamentoSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            EquipoSeeder::class,
            ContratistasSeeder::class,
            NovedadSeeder::class,
            CategoriaReporteSeeder::class,
            TipoReporteSeeder::class,
            ReporteSeeder::class,
            EstadoReporteSeeder::class,
            TipoIntervencionSeeder::class,
            ReporteTecnicoSeeder::class,
            HistorialEstadoReporteSeeder::class,
            ColaboradorSeeder::class, 
            
        ]);
    }
}
