<?php

namespace App\Filament\Resources\RegistrarTurnoResource\Pages;

use App\Filament\Resources\RegistrarTurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrarTurno extends EditRecord
{
    protected static string $resource = RegistrarTurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
