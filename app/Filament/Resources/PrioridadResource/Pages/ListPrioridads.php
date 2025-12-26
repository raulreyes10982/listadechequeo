<?php

namespace App\Filament\Resources\PrioridadResource\Pages;

use App\Filament\Resources\PrioridadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrioridads extends ListRecords
{
    protected static string $resource = PrioridadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
