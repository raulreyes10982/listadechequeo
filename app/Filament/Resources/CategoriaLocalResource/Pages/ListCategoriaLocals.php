<?php

namespace App\Filament\Resources\CategoriaLocalResource\Pages;

use App\Filament\Resources\CategoriaLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriaLocals extends ListRecords
{
    protected static string $resource = CategoriaLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('lg'),
        ];
    }
}
