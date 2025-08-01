<?php

namespace App\Filament\Resources\PrioridadResource\Pages;

use App\Filament\Resources\PrioridadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrioridad extends EditRecord
{
    protected static string $resource = PrioridadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
