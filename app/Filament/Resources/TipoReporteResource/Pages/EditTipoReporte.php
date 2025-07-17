<?php

namespace App\Filament\Resources\TipoReporteResource\Pages;

use App\Filament\Resources\TipoReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoReporte extends EditRecord
{
    protected static string $resource = TipoReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
