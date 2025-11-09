<?php

namespace App\Filament\Resources\GrupoSanguineoResource\Pages;

use App\Filament\Resources\GrupoSanguineoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGrupoSanguineos extends ListRecords
{
    protected static string $resource = GrupoSanguineoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
            
        ];
    }
}
