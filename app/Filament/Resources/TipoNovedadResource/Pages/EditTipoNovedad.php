<?php

namespace App\Filament\Resources\TipoNovedadResource\Pages;

use App\Filament\Resources\TipoNovedadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoNovedad extends EditRecord
{
    protected static string $resource = TipoNovedadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
