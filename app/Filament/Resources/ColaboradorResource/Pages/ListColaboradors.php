<?php

namespace App\Filament\Resources\ColaboradorResource\Pages;

use App\Filament\Resources\ColaboradorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListColaboradors extends ListRecords
{
    protected static string $resource = ColaboradorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Usa getModalWidth() del Resource — mismo ancho que el modal de editar
            Actions\CreateAction::make()
                ->label('Crear colaborador')
                ->modalWidth(ColaboradorResource::getModalWidth()),
        ];
    }
}
