<?php

namespace App\Filament\Resources\HistorialEstadoReporteResource\Pages;

use App\Filament\Resources\HistorialEstadoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistorialEstadoReportes extends ListRecords
{
    protected static string $resource = HistorialEstadoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
