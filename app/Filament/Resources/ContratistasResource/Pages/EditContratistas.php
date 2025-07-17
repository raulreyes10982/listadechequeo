<?php

namespace App\Filament\Resources\ContratistasResource\Pages;

use App\Filament\Resources\ContratistasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContratistas extends EditRecord
{
    protected static string $resource = ContratistasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
