<?php

namespace App\Filament\Resources\TipoEquipoResource\Pages;

use App\Filament\Resources\TipoEquipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoEquipos extends ListRecords
{
    protected static string $resource = TipoEquipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\CreateAction::make()->modalWidth('lg'),

        ];
    }
}
