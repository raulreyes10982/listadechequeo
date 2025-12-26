<?php

namespace App\Filament\Resources\HistorialVerificacionResource\Pages;

use App\Filament\Resources\HistorialVerificacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorialVerificacion extends EditRecord
{
    protected static string $resource = HistorialVerificacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
