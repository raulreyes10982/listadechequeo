<?php

namespace App\Filament\Resources\ColaboradorResource\Pages;

use App\Filament\Resources\ColaboradorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditColaborador extends EditRecord
{
    protected static string $resource = ColaboradorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ✅ Botón de eliminar en el header de la página de edición
            Actions\DeleteAction::make()
                ->label('Eliminar colaborador')
                ->requiresConfirmation()
                ->modalHeading('¿Eliminar colaborador?')
                ->modalDescription('Esta acción no se puede deshacer. Se eliminarán también sus documentos adjuntos.'),
        ];
    }

    // ✅ Después de guardar, muestra notificación y se queda en la página
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Colaborador actualizado correctamente';
    }

    // ✅ Al guardar, recalcular edad si cambió fecha_nacimiento
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['fecha_nacimiento']) && $data['fecha_nacimiento']) {
            $data['edad'] = \Carbon\Carbon::parse($data['fecha_nacimiento'])->age;
        }
        return $data;
    }
}
