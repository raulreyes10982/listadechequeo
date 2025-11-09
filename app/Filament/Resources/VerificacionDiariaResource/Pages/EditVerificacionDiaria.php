<?php

namespace App\Filament\Resources\VerificacionDiariaResource\Pages;

use App\Filament\Resources\VerificacionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerificacionDiaria extends EditRecord
{
    protected static string $resource = VerificacionDiariaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
