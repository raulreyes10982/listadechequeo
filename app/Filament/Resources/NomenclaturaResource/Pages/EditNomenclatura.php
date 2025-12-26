<?php

namespace App\Filament\Resources\NomenclaturaResource\Pages;

use App\Filament\Resources\NomenclaturaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNomenclatura extends EditRecord
{
    protected static string $resource = NomenclaturaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
