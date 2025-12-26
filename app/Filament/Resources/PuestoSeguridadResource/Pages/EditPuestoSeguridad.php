<?php

namespace App\Filament\Resources\PuestoSeguridadResource\Pages;

use App\Filament\Resources\PuestoSeguridadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuestoSeguridad extends EditRecord
{
    protected static string $resource = PuestoSeguridadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
