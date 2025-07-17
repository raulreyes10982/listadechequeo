<?php

namespace App\Filament\Resources\NovedadResource\Pages;

use App\Filament\Resources\NovedadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNovedads extends ListRecords
{
    protected static string $resource = NovedadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
