<?php

namespace App\Filament\Resources\HistoriaLaboralResource\Pages;

use App\Filament\Resources\HistoriaLaboralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoriaLaboral extends EditRecord
{
    protected static string $resource = HistoriaLaboralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
