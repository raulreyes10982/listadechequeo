<?php

namespace App\Filament\Resources\ColaboradorResource\Pages;

use App\Filament\Resources\ColaboradorResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateColaborador extends CreateRecord
{
    protected static string $resource = ColaboradorResource::class;

    // ✅ Después de crear, redirige a la página de edición
    // para que el admin pueda subir documentos inmediatamente
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', [
            'record' => $this->getRecord(),
        ]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Colaborador creado correctamente';
    }

    // ✅ Calcular edad automáticamente al crear
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['fecha_nacimiento'])) {
            $data['edad'] = Carbon::parse($data['fecha_nacimiento'])->age;
        }
        return $data;
    }
}
