<?php

namespace App\Filament\Resources\TipoIntervencionResource\Pages;

use App\Filament\Resources\TipoIntervencionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoIntervencion extends EditRecord
{
    protected static string $resource = TipoIntervencionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
