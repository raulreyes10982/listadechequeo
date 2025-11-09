<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoDocumento;

class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Registro civil',
            'Tarjeta de identidad',
            'Cédula de ciudadanía',
            'Cédula de extranjería',
            'Pasaporte',
            'Permiso Especial de Permanencia (PEP)',
            'Documento de identidad extranjero',
            'Número de Identificación Tributaria (NIT)',
            'Salvoconducto',
            'Permiso por Protección Temporal (PPT)',
        ];

        foreach ($tipos as $tipo) {
            TipoDocumento::create([
                'descripcion' => $tipo,
            ]);
        }
    }
}
