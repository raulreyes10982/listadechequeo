<?php

namespace App\Filament\Resources\ReporteTecnicoResource\Pages;

use App\Filament\Resources\ReporteTecnicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReporteTecnicos extends ListRecords
{
    protected static string $resource = ReporteTecnicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
               Actions\CreateAction::make()->modalWidth('3xl'),
        ];
    }
}
