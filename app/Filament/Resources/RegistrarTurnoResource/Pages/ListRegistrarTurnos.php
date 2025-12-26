<?php

namespace App\Filament\Resources\RegistrarTurnoResource\Pages;

use App\Filament\Resources\RegistrarTurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrarTurnos extends ListRecords
{
    protected static string $resource = RegistrarTurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
            Actions\CreateAction::make()->modalWidth(),
        ];
    }
}
