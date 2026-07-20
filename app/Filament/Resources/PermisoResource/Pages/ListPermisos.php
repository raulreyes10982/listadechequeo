<?php

namespace App\Filament\Resources\PermisoResource\Pages;

use App\Filament\Resources\PermisoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;

class ListPermisos extends ListRecords
{
    protected static string $resource = PermisoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Crear en modal — igual al editar
            Actions\CreateAction::make()
                ->label('+ Nuevo')
                ->modalWidth(MaxWidth::FourExtraLarge)
                ->modalHeading('Crear Permiso de trabajo')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['subidopor'] = Auth::user()->name ?? 'Sistema';
                    return $data;
                }),
        ];
    }
}
