<?php

namespace App\Filament\Resources\GrupoSanguineoResource\Pages;

use App\Filament\Resources\GrupoSanguineoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrupoSanguineo extends EditRecord
{
    protected static string $resource = GrupoSanguineoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
