<?php

namespace App\Filament\Resources\EstadoReporteResource\Pages;

use App\Filament\Resources\EstadoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstadoReporte extends EditRecord
{
    protected static string $resource = EstadoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
