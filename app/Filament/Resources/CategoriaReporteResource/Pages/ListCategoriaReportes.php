<?php

namespace App\Filament\Resources\CategoriaReporteResource\Pages;

use App\Filament\Resources\CategoriaReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaReportes extends ListRecords
{
    protected static string $resource = CategoriaReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
