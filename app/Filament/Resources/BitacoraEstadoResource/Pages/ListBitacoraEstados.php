<?php

namespace App\Filament\Resources\BitacoraEstadoResource\Pages;

use App\Filament\Resources\BitacoraEstadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraEstados extends ListRecords
{
    protected static string $resource = BitacoraEstadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
