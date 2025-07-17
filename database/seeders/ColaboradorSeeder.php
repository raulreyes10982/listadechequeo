<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Colaborador;
use App\Models\Departamento;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\TipoContrato;
use App\Models\TipoDocumento;
use App\Models\EstadoCivil;
use App\Models\Genero;
use App\Models\GrupoSanguineo;
use Illuminate\Support\Str;

class ColaboradorSeeder extends Seeder
{
    public function run(): void
    {
        $documentoBase = 1000000000;

        foreach (Departamento::all() as $departamento) {
            $areas = Area::where('departamento_id', $departamento->id)->get();

            for ($i = 0; $i < 3; $i++) {
                $area = $areas->random();
                $cargo = Cargo::where('area_id', $area->id)->inRandomOrder()->first();

                Colaborador::create([
                    'nombre' => 'Nombre' . $i,
                    'apellido' => 'Apellido' . $i,
                    'celular' => rand(3000000000, 3200000000),
                    'documento' => $documentoBase + $i + ($departamento->id * 10),
                    'lugarnacimiento' => 'Ciudad ' . $i,
                    'telefono' => rand(6000000, 6999999),
                    'fecha_nacimiento' => now()->subYears(rand(20, 40))->subDays(rand(0, 365)),
                    'edad' => rand(20, 40),
                    'barrio' => 'Barrio ' . $i,
                    'direccion' => 'Calle Falsa ' . rand(1, 100),
                    'correo_corporativo' => 'colab' . $departamento->id . $i . '@empresa.com',
                    'correo_personal' => 'colab' . $departamento->id . $i . '@gmail.com',
                    'fechainiciolab' => now()->subMonths(rand(1, 12)),
                    'fechafinlab' => null,

                    'tipo_documento_id' => TipoDocumento::inRandomOrder()->first()->id,
                    'estado_civil_id' => EstadoCivil::inRandomOrder()->first()->id,
                    'departamento_id' => $departamento->id,
                    'area_id' => $area->id,
                    'cargo_id' => $cargo->id,
                    'tipo_contrato_id' => TipoContrato::inRandomOrder()->first()->id,
                    'genero_id' => Genero::inRandomOrder()->first()->id,
                    'grupo_sanguineo_id' => GrupoSanguineo::inRandomOrder()->first()->id,
                ]);
            }
        }
    }
}
