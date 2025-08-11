<?php

namespace App\Filament\Resources\ReporteTecnicoResource\Pages;

use App\Filament\Resources\ReporteTecnicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReporteTecnico extends EditRecord
{
    protected static string $resource = ReporteTecnicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
            Actions\DeleteAction::make()->modalWidth('3xl'),
        ];
    }

    
}
