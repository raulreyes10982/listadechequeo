<?php

namespace App\Filament\Resources\ListReporteTurnosDiariosResource\Pages;

use App\Filament\Resources\ListReporteTurnosDiariosResource;
use Filament\Resources\Pages\ListRecords;

class ListListReporteTurnosDiarios extends ListRecords
{
    protected static string $resource = ListReporteTurnosDiariosResource::class;

    // ✅ Sin botón "Nuevo" — este Resource es solo de consulta y descarga
    protected function getHeaderActions(): array
    {
        return [];
    }
}
