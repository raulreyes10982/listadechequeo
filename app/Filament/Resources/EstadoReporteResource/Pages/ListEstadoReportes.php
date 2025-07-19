<?php

namespace App\Filament\Resources\EstadoReporteResource\Pages;

use App\Filament\Resources\EstadoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoReportes extends ListRecords
{
    protected static string $resource = EstadoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
