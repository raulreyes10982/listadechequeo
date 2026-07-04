<?php

namespace App\Filament\Resources\HistoriaLaboralResource\Pages;

use App\Filament\Resources\HistoriaLaboralResource;
use Filament\Resources\Pages\ListRecords;

class ListHistoriaLaborals extends ListRecords
{
    protected static string $resource = HistoriaLaboralResource::class;

    // Solo lectura — sin botón "Nuevo"
    protected function getHeaderActions(): array
    {
        return [];
    }
}
