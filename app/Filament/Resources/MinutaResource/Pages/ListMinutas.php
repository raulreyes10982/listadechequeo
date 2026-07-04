<?php

namespace App\Filament\Resources\MinutaResource\Pages;

use App\Filament\Resources\MinutaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMinutas extends ListRecords
{
    protected static string $resource = MinutaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
