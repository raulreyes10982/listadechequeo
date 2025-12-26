<?php

namespace App\Filament\Resources\CategoriaReporteResource\Pages;

use App\Filament\Resources\CategoriaReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaReporte extends EditRecord
{
    protected static string $resource = CategoriaReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
