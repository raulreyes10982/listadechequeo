<?php

namespace App\Filament\Resources\VerificacionTurnoResource\Pages;

use App\Filament\Resources\VerificacionTurnoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerificacionTurno extends EditRecord
{
    protected static string $resource = VerificacionTurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
