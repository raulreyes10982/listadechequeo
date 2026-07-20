<?php

namespace App\Filament\Resources\ColaboradorResource\Pages;

use App\Filament\Resources\ColaboradorResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListColaboradors extends ListRecords
{
    protected static string $resource = ColaboradorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Crear también en modal — sin cambiar de página
            Actions\CreateAction::make()
                ->label('Nuevo colaborador')
                ->modalWidth(MaxWidth::FiveExtraLarge)
                ->mutateFormDataUsing(function (array $data): array {
                    if (! empty($data['fecha_nacimiento'])) {
                        $data['edad'] = Carbon::parse($data['fecha_nacimiento'])->age;
                    }
                    return $data;
                }),
        ];
    }
}
