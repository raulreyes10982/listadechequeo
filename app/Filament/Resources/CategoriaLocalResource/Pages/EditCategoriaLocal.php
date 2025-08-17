<?php

namespace App\Filament\Resources\CategoriaLocalResource\Pages;

use App\Filament\Resources\CategoriaLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaLocal extends EditRecord
{
    protected static string $resource = CategoriaLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
