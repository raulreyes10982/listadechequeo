<?php

namespace App\Filament\Resources\VerificacionTurnoResource\Pages;

use App\Filament\Resources\VerificacionTurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerificacionTurnos extends ListRecords
{
    protected static string $resource = VerificacionTurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
