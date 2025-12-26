<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrabajadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de trabajadores tomada de tu imagen
        // Documento => Nombre
        $trabajadores = [
            ['documento' => '10190439', 'nombre' => 'Gilberto Avila Avila'],
            ['documento' => '80872039', 'nombre' => 'Piedad Yanet Rico Martinez'],
            ['documento' => '1011142944', 'nombre' => 'Gustavo Adolfo Garcia'],
            ['documento' => '79549396', 'nombre' => 'Jorge Humberto Olarte Rodriguez'],
            ['documento' => '52160058', 'nombre' => 'Andres Alfonso Ochoa'],
            ['documento' => '80321522', 'nombre' => 'Claudia Rocio Vargas Fandiño'],
            ['documento' => '1020745481', 'nombre' => 'Yuly Andrea Angarita Cubillos'],

            ['documento' => '1020745481', 'nombre' => 'Angarita Cubillos Yuly Andrea'],
            ['documento' => '1014282541', 'nombre' => 'Marley Johana Garcia Garcia'],

            ['documento' => '1065768565', 'nombre' => 'Ricardo Andres Sanchez Riaño'],
            ['documento' => '52031614', 'nombre' => 'Martha Fernandez De Mesa'],
            ['documento' => '1014244088', 'nombre' => 'Jorge Enrique Barbosa Gonzalez'],
            ['documento' => '79115063', 'nombre' => 'Ceidy Yadira Campos Pereira'],

            ['documento' => '1019060971', 'nombre' => 'Mauricio Enrique Portillo Gaona'],
            ['documento' => '79331099', 'nombre' => 'Hugo Fernando Bedoya Arroyave'],
            ['documento' => '1014282541', 'nombre' => 'Marley Jhoana García García'],
            ['documento' => '52514259', 'nombre' => 'Maria Eugenia Triana Romero'],

            ['documento' => '1020745481', 'nombre' => 'Yuly Andrea Angarita Cubillos'],
            ['documento' => '1014282541', 'nombre' => 'Marley Jhoana Garcia Garcia'],
            ['documento' => '52514259', 'nombre' => 'Maria Eugenia Triana Romero'],
            ['documento' => '1019060971', 'nombre' => 'Mauricio Enrique Portillo Gaona'],
            ['documento' => '79331099', 'nombre' => 'Hugo Fernando Bedoya Arroyave'],
        ];

      // El seed de permisos crea 10 permisos (id 1 a 10)
$totalPermisos = 10;
$permisoActual = 1;

foreach ($trabajadores as $t) {

    // Extraer datos del array
    $documento = $t['documento'];
    $nombre    = $t['nombre'];
    $permisoId = $permisoActual;

    DB::table('trabajadores')->updateOrInsert(
        [
            'permiso_id' => $permisoId,
            'documento' => $documento,
        ],
        [
            'nombre' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    // Rotación automática de permisos
    $permisoActual++;
    if ($permisoActual > $totalPermisos) {
        $permisoActual = 1;
    }
}
    }
}
