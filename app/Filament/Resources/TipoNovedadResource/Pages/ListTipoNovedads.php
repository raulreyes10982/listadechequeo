<?php

namespace App\Filament\Resources\TipoNovedadResource\Pages;

use App\Filament\Resources\TipoNovedadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoNovedads extends ListRecords
{
    protected static string $resource = TipoNovedadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\CreateAction::make()->modalWidth('lg'),

        ];
    }
}
