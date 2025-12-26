<?php

namespace App\Filament\Resources\ContratistasResource\Pages;

use App\Filament\Resources\ContratistasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContratistas extends ListRecords
{
    protected static string $resource = ContratistasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
