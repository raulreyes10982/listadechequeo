<?php

namespace Database\Seeders;

use App\Models\CategoriaLocal;
use App\Models\EstadoReporte;
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
        //Base Laravel
        //UserSeeder::class,

        // Catálogos
        DepartamentoSeeder::class,
        AreaSeeder::class,
        CargoSeeder::class,
        GeneroSeeder::class,
        TipoDocumentoSeeder::class,
        TipoContratoSeeder::class,
        EstadoCivilSeeder::class,
        GrupoSanguineoSeeder::class,

        // Personal
        ColaboradorSeeder::class,
        ContratistasSeeder::class,

        // Equipos y locales
        TipoEquipoSeeder::class,
        EquipoSeeder::class,
        CategoriaLocalSeeder::class,
        NomenclaturaSeeder::class,
        LocalSeeder::class,
        PuestoSeguridadSeeder::class,

        // Reportes y novedades
        CategoriaReporteSeeder::class,
        TipoReporteSeeder::class,
        TipoIntervencionSeeder::class,
        EstadoReporteSeeder::class,
        PrioridadSeeder::class,
        EstadoSeeder::class,
        ZonaSeeder::class,
        UbicacionSeeder::class,
        ReporteTecnicoSeeder::class,
        HistorialEstadoReporteSeeder::class,
        ReporteSeeder::class,
        //BitacoraEstadoSeeder::class,
        TipoNovedadSeeder::class,
        NovedadSeeder::class,

        // Permisos
        TipoPermisoSeeder::class,
        PermisoSeeder::class,
        TrabajadorSeeder::class,
        //VerificacionDiariaSeeder::class,
            
        ]);
    }
}