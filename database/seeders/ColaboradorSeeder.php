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

class ColaboradorSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Datos reales extraídos del Excel NUEVA_BASE_DE_DATOS.xlsx
    | 20 registros — los campos tipo_documento, estado_civil, etc. se resuelven
    | buscando el registro existente por descripción; si no existe, se toma
    | el primero disponible como fallback.
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        // ── Helpers para resolver IDs por descripción ──────────────────────

        $tipoDoc = fn(string $tipo) => TipoDocumento::where('descripcion', 'like', "%{$tipo}%")
            ->first()?->id ?? TipoDocumento::first()?->id;

        $estadoCivil = fn(string $ec) => EstadoCivil::where('descripcion', 'like', "%{$ec}%")
            ->first()?->id ?? EstadoCivil::first()?->id;

        $genero = fn(string $g) => Genero::where('descripcion', 'like', "%{$g}%")
            ->first()?->id ?? Genero::first()?->id;

        $grupoSang = fn() => GrupoSanguineo::inRandomOrder()->first()?->id
            ?? GrupoSanguineo::first()?->id;

        $tipoContrato = fn() => TipoContrato::inRandomOrder()->first()?->id
            ?? TipoContrato::first()?->id;

        // Resolver departamento → área → cargo en cascada
        $resolverCargo = function (string $depto) {
            $departamento = Departamento::inRandomOrder()->first();
            $area = Area::where('departamento_id', $departamento->id)->inRandomOrder()->first()
                ?? Area::inRandomOrder()->first();
            $cargo = Cargo::where('area_id', $area->id)->inRandomOrder()->first()
                ?? Cargo::inRandomOrder()->first();

            return [$departamento->id, $area->id, $cargo->id];
        };

        // ── Datos reales (20 colaboradores) ────────────────────────────────
        $colaboradores = [
            [
                'nombre'         => 'Maribel',
                'apellido'       => 'Riaño Delgado',
                'documento'      => '37721615',
                'celular'        => '3132738122',
                'telefono'       => '5781850',
                'direccion'      => 'Calle 1 Nº 11-39 Av 11/12',
                'barrio'         => 'Comuneros',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'jepiffano@aca.com.co',
                'correo_corporativo' => 'maribel.riano@empresa.com',
                'fecha_nacimiento'=> '1978-11-30',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Sandra Milena',
                'apellido'       => 'Pinto',
                'documento'      => '37721887',
                'celular'        => '3133490556',
                'telefono'       => '5652298',
                'direccion'      => 'Cll 0 A # 3-73',
                'barrio'         => 'Monaco',
                'lugarnacimiento'=> 'Villa del Rosario',
                'correo_personal'=> 'caerblasu_47@hotmail.com',
                'correo_corporativo' => 'sandram.pinto@empresa.com',
                'fecha_nacimiento'=> '1978-10-07',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Eddys',
                'apellido'       => 'Osorio Paez',
                'documento'      => '37722538',
                'celular'        => '3202328485',
                'telefono'       => '',
                'direccion'      => 'Manz 12 Casa 2',
                'barrio'         => 'Betania',
                'lugarnacimiento'=> 'Los Patios',
                'correo_personal'=> 'mijapri1908@hotmail.com',
                'correo_corporativo' => 'eddys.osorio@empresa.com',
                'fecha_nacimiento'=> '1979-01-18',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Masculino',
            ],
            [
                'nombre'         => 'Glendy Lizbeth',
                'apellido'       => 'Torres Hernandez',
                'documento'      => '37722622',
                'celular'        => '3112065214',
                'telefono'       => '',
                'direccion'      => 'Cll 11 Nº16-73',
                'barrio'         => 'Cundinamarca',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'glendy.torres@gmail.com',
                'correo_corporativo' => 'glendy.torres@empresa.com',
                'fecha_nacimiento'=> '1979-01-15',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Laura',
                'apellido'       => 'Mendez',
                'documento'      => '37722652',
                'celular'        => '3004935517',
                'telefono'       => '',
                'direccion'      => 'Cll 22 # 0 - 86',
                'barrio'         => 'Barrio Blanco',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'laura.mendez@gmail.com',
                'correo_corporativo' => 'laura.mendez@empresa.com',
                'fecha_nacimiento'=> '1978-12-06',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Yackeline',
                'apellido'       => 'Ojeda',
                'documento'      => '37723023',
                'celular'        => '3103648257',
                'telefono'       => '',
                'direccion'      => 'Prados Norte Cll 21N 2-101',
                'barrio'         => 'Prados Norte',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'yampor.84@hotmail.com',
                'correo_corporativo' => 'yackeline.ojeda@empresa.com',
                'fecha_nacimiento'=> '1979-01-12',
                'edad'           => 46,
                'estado_civil'   => 'Unión libre',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Lenny Amparo',
                'apellido'       => 'Correa Angarita',
                'documento'      => '37723051',
                'celular'        => '3125827946',
                'telefono'       => '5750143',
                'direccion'      => 'Cll 19AN Nº18E-10',
                'barrio'         => 'Niza',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'desire.jaimes1912@gmail.com',
                'correo_corporativo' => 'lenny.correa@empresa.com',
                'fecha_nacimiento'=> '1979-01-31',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Maela',
                'apellido'       => 'Peñuela',
                'documento'      => '37723099',
                'celular'        => '3214805269',
                'telefono'       => '5749596',
                'direccion'      => 'Manzana D11, Lote 12',
                'barrio'         => 'Torcoroma',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'zorayalejandra@hotmail.com',
                'correo_corporativo' => 'maela.penuela@empresa.com',
                'fecha_nacimiento'=> '1979-01-25',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Patricia',
                'apellido'       => 'Galofre Rodriguez',
                'documento'      => '37723220',
                'celular'        => '3108761836',
                'telefono'       => '',
                'direccion'      => 'Cll 2 Nº 4E-99',
                'barrio'         => 'La Ceiba',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'productos.unicoentodo@hotmail.com',
                'correo_corporativo' => 'patricia.galofre@empresa.com',
                'fecha_nacimiento'=> '1979-01-09',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Darlys Daneyda',
                'apellido'       => 'Medina Posso',
                'documento'      => '37723240',
                'celular'        => '3134330681',
                'telefono'       => '3124373783',
                'direccion'      => 'Cll 26 Nº19A-24',
                'barrio'         => 'Centro',
                'lugarnacimiento'=> 'Arauca',
                'correo_personal'=> 'darlys.medina@gmail.com',
                'correo_corporativo' => 'darlys.medina@empresa.com',
                'fecha_nacimiento'=> '1978-05-25',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Sandra Milena',
                'apellido'       => 'Lopez Jaimes',
                'documento'      => '37723357',
                'celular'        => '3132793074',
                'telefono'       => '',
                'direccion'      => 'Urb Pensi Av 10B # 1S-06',
                'barrio'         => 'Pensilvania',
                'lugarnacimiento'=> 'Los Patios',
                'correo_personal'=> 'sandralopez.jaimes@gmail.com',
                'correo_corporativo' => 'sandral.lopez@empresa.com',
                'fecha_nacimiento'=> '1978-09-12',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Flor Edilma',
                'apellido'       => 'Estupiñan Buitrago',
                'documento'      => '37723563',
                'celular'        => '3104306138',
                'telefono'       => '',
                'direccion'      => 'Centro, Bucaramanga',
                'barrio'         => 'Centro',
                'lugarnacimiento'=> 'Bucaramanga',
                'correo_personal'=> 'monik.87@hotmail.com',
                'correo_corporativo' => 'flor.estupinian@empresa.com',
                'fecha_nacimiento'=> '1978-08-05',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Johanna',
                'apellido'       => 'Pineda',
                'documento'      => '37723634',
                'celular'        => '3163326825',
                'telefono'       => '',
                'direccion'      => 'Villa Parque Casa 57',
                'barrio'         => 'Centro Comercial Bolivar',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'gianna.jaimes@hotmail.com',
                'correo_corporativo' => 'johanna.pineda@empresa.com',
                'fecha_nacimiento'=> '1978-11-04',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Paola Andrea',
                'apellido'       => 'Escobar',
                'documento'      => '37723762',
                'celular'        => '3204267763',
                'telefono'       => '',
                'direccion'      => 'Urb. Villas de San Diego Casa C19',
                'barrio'         => 'Bocono',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'paola.escobar@gmail.com',
                'correo_corporativo' => 'paola.escobar@empresa.com',
                'fecha_nacimiento'=> '1978-11-06',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Ana Milena',
                'apellido'       => 'Parra Velez',
                'documento'      => '37723944',
                'celular'        => '3208523517',
                'telefono'       => '5896164',
                'direccion'      => 'Cll 9 Nº6A-15',
                'barrio'         => 'El Escobal',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'milepima@yahoo.com',
                'correo_corporativo' => 'anam.parra@empresa.com',
                'fecha_nacimiento'=> '1979-02-26',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Ivette Angelica',
                'apellido'       => 'Paba Leon',
                'documento'      => '37724153',
                'celular'        => '3107066777',
                'telefono'       => '',
                'direccion'      => 'Calle 8 Nº 4-103 Guamal',
                'barrio'         => 'Centro',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'estefaniarivera_24@hotmail.com',
                'correo_corporativo' => 'ivette.paba@empresa.com',
                'fecha_nacimiento'=> '1978-08-06',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Maria Isabel',
                'apellido'       => 'Quiroz Arias',
                'documento'      => '37724262',
                'celular'        => '3152843756',
                'telefono'       => '6480300',
                'direccion'      => 'Cra 18 Nº 3-48',
                'barrio'         => 'Jardin de Limonsillo',
                'lugarnacimiento'=> 'Floridablanca',
                'correo_personal'=> 'leidydiaz2007@hotmail.com',
                'correo_corporativo' => 'mariai.quiroz@empresa.com',
                'fecha_nacimiento'=> '1977-06-03',
                'edad'           => 47,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Ruth Karla',
                'apellido'       => 'Suarez',
                'documento'      => '37724322',
                'celular'        => '3106086105',
                'telefono'       => '5746567',
                'direccion'      => 'Av 0A 0N-64',
                'barrio'         => 'Lleras',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'jl2526@hotmail.com.co',
                'correo_corporativo' => 'ruth.suarez@empresa.com',
                'fecha_nacimiento'=> '1979-03-14',
                'edad'           => 46,
                'estado_civil'   => 'Casado',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Dora Marcela',
                'apellido'       => 'Parra',
                'documento'      => '37724382',
                'celular'        => '3176575396',
                'telefono'       => '',
                'direccion'      => 'Cll 8 # 6 - 25',
                'barrio'         => 'Pamplona',
                'lugarnacimiento'=> 'Pamplona',
                'correo_personal'=> 'dora.parra@gmail.com',
                'correo_corporativo' => 'dora.parra@empresa.com',
                'fecha_nacimiento'=> '1979-02-20',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
            [
                'nombre'         => 'Nelfa',
                'apellido'       => 'Dominguez',
                'documento'      => '37724434',
                'celular'        => '3203447047',
                'telefono'       => '',
                'direccion'      => 'Cll 11 # 11 -34',
                'barrio'         => 'El Llano',
                'lugarnacimiento'=> 'Cúcuta',
                'correo_personal'=> 'jorgeramon_91@hotmail.com',
                'correo_corporativo' => 'nelfa.dominguez@empresa.com',
                'fecha_nacimiento'=> '1978-10-15',
                'edad'           => 46,
                'estado_civil'   => 'Soltero',
                'genero'         => 'Femenino',
            ],
        ];

        // ── Insertar cada colaborador ───────────────────────────────────────
        foreach ($colaboradores as $data) {
            // Evitar duplicados por documento
            if (Colaborador::where('documento', $data['documento'])->exists()) {
                $this->command->warn("  ⏭ Omitido (ya existe): {$data['nombre']} {$data['apellido']} — doc {$data['documento']}");
                continue;
            }

            [$deptoId, $areaId, $cargoId] = $resolverCargo($data['lugarnacimiento']);

            Colaborador::create([
                'nombre'            => $data['nombre'],
                'apellido'          => $data['apellido'],
                'documento'         => $data['documento'],
                'celular'           => $data['celular'],
                'telefono'          => $data['telefono'] ?: null,
                'direccion'         => $data['direccion'],
                'barrio'            => $data['barrio'],
                'lugarnacimiento'   => $data['lugarnacimiento'],
                'correo_personal'   => $data['correo_personal'],
                'correo_corporativo'=> $data['correo_corporativo'],
                'fecha_nacimiento'  => $data['fecha_nacimiento'],
                'edad'              => $data['edad'],
                'fechainiciolab'    => now()->subMonths(rand(6, 24))->toDateString(),
                'fechafinlab'       => null,

                // Relaciones — se resuelven por descripción con fallback
                'tipo_documento_id' => $tipoDoc('Cédula'),
                'estado_civil_id'   => $estadoCivil($data['estado_civil']),
                'genero_id'         => $genero($data['genero']),
                'grupo_sanguineo_id'=> $grupoSang(),
                'tipo_contrato_id'  => $tipoContrato(),
                'departamento_id'   => $deptoId,
                'area_id'           => $areaId,
                'cargo_id'          => $cargoId,
            ]);

            $this->command->info("  ✅ Creado: {$data['nombre']} {$data['apellido']} — {$data['documento']}");
        }

        $this->command->info('');
        $this->command->info('✅ ColaboradorSeeder completado — 20 registros procesados.');
    }
}
