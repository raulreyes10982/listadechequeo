<?php

namespace App\Filament\Resources\ListReporteTurnosDiariosResource\Pages;

use App\Filament\Resources\ListReporteTurnosDiariosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListReporteTurnosDiarios extends EditRecord
{
    protected static string $resource = ListReporteTurnosDiariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
