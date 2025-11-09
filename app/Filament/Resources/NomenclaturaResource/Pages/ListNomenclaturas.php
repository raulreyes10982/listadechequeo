<?php

namespace App\Filament\Resources\NomenclaturaResource\Pages;

use App\Filament\Resources\NomenclaturaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNomenclaturas extends ListRecords
{
    protected static string $resource = NomenclaturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
