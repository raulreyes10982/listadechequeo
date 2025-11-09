<?php

namespace App\Filament\Resources\TipoReporteResource\Pages;

use App\Filament\Resources\TipoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoReportes extends ListRecords
{
    protected static string $resource = TipoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
