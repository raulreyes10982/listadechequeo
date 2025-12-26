<?php

namespace App\Filament\Resources\TipoPermisoResource\Pages;

use App\Filament\Resources\TipoPermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoPermiso extends EditRecord
{
    protected static string $resource = TipoPermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->modalWidth('lg'),
        ];
    }
}
