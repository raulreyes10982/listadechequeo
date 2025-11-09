<?php

namespace App\Filament\Resources\VerificacionDiariaResource\Pages;

use App\Filament\Resources\VerificacionDiariaResource;
use Filament\Resources\Pages\ListRecords;

class ListVerificacionDiarias extends ListRecords
{
    protected static string $resource = VerificacionDiariaResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Sin crear/editar desde aquí
    }

    public function getTitle(): string
    {
        return 'Personal autorizado'; // como en tu captura
    }
}
