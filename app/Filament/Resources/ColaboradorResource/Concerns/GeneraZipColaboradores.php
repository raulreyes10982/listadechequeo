<?php

namespace App\Filament\Resources\ColaboradorResource\Concerns;

use App\Models\Colaborador;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use ZipArchive;

/**
 * Trait con la lógica para generar ZIPs de documentos de colaboradores.
 *
 * - descargarZipIndividual(): ZIP con los documentos de UN colaborador
 * - descargarZipGrupal(): ZIP con carpetas por cada colaborador seleccionado
 */
trait GeneraZipColaboradores
{
    /**
     * ZIP de un solo colaborador — todos sus documentos en la raíz del zip.
     */
    public static function descargarZipIndividual(Colaborador $colaborador)
    {
        $documentos = $colaborador->documentos;

        if ($documentos->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin documentos')
                ->body("{$colaborador->nombre} {$colaborador->apellido} no tiene documentos cargados.")
                ->warning()
                ->send();
            return null;
        }

        $nombreZip = "documentos_{$colaborador->documento}_" .
            str(trim("{$colaborador->nombre} {$colaborador->apellido}"))->slug() . '.zip';

        $rutaTemp = storage_path("app/temp/{$nombreZip}");

        if (! is_dir(dirname($rutaTemp))) {
            mkdir(dirname($rutaTemp), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($rutaTemp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($documentos as $doc) {
            $rutaArchivo = Storage::disk('local')->path($doc->archivo);

            if (file_exists($rutaArchivo)) {
                $extension = pathinfo($doc->archivo, PATHINFO_EXTENSION);
                $nombreEnZip = str($doc->nombre)->slug() . '.' . $extension;
                $zip->addFile($rutaArchivo, $nombreEnZip);
            }
        }

        $zip->close();

        return Response::download($rutaTemp, $nombreZip)->deleteFileAfterSend(true);
    }

    /**
     * ZIP grupal — una carpeta por colaborador con sus documentos dentro.
     *
     * @param \Illuminate\Support\Collection<Colaborador> $colaboradores
     */
    public static function descargarZipGrupal($colaboradores)
    {
        $colaboradores = $colaboradores->load('documentos');

        $conDocumentos = $colaboradores->filter(fn ($c) => $c->documentos->isNotEmpty());

        if ($conDocumentos->isEmpty()) {
            \Filament\Notifications\Notification::make()
                ->title('Sin documentos')
                ->body('Ninguno de los colaboradores seleccionados tiene documentos cargados.')
                ->warning()
                ->send();
            return null;
        }

        $fecha     = now()->format('d-m-Y');
        $nombreZip = "documentos_colaboradores_{$fecha}.zip";
        $rutaTemp  = storage_path("app/temp/{$nombreZip}");

        if (! is_dir(dirname($rutaTemp))) {
            mkdir(dirname($rutaTemp), 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($rutaTemp, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($conDocumentos as $colaborador) {
            // Carpeta: "37721615 - Maribel Riaño Delgado"
            $carpeta = $colaborador->documento . ' - ' .
                trim("{$colaborador->nombre} {$colaborador->apellido}");

            foreach ($colaborador->documentos as $doc) {
                $rutaArchivo = Storage::disk('local')->path($doc->archivo);

                if (file_exists($rutaArchivo)) {
                    $extension   = pathinfo($doc->archivo, PATHINFO_EXTENSION);
                    $nombreEnZip = $carpeta . '/' . str($doc->nombre)->slug() . '.' . $extension;
                    $zip->addFile($rutaArchivo, $nombreEnZip);
                }
            }
        }

        $zip->close();

        return Response::download($rutaTemp, $nombreZip)->deleteFileAfterSend(true);
    }
}
