<?php

namespace App\Filament\Resources\PuestoSeguridadResource\Pages;

use App\Filament\Resources\PuestoSeguridadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPuestoSeguridads extends ListRecords
{
    protected static string $resource = PuestoSeguridadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\CreateAction::make()->modalWidth(),
        ];
    }
}
