<?php

namespace App\Filament\Resources\HistorialVerificacionResource\Pages;

use App\Filament\Resources\HistorialVerificacionResource;
use Filament\Resources\Pages\ListRecords;

class ListHistorialVerificaciones extends ListRecords
{
    protected static string $resource = HistorialVerificacionResource::class;

    protected function getHeaderActions(): array
    {
        return []; // sin acciones de crear
    }

    public function getTitle(): string
    {
        return 'Personal verificado';
    }
}
