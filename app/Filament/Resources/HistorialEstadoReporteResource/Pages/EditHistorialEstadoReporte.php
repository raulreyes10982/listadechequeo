<?php

namespace App\Filament\Resources\HistorialEstadoReporteResource\Pages;

use App\Filament\Resources\HistorialEstadoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistorialEstadoReporte extends EditRecord
{
    protected static string $resource = HistorialEstadoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
