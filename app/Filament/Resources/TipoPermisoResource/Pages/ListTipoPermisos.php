<?php

namespace App\Filament\Resources\TipoPermisoResource\Pages;

use App\Filament\Resources\TipoPermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoPermisos extends ListRecords
{
    protected static string $resource = TipoPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
