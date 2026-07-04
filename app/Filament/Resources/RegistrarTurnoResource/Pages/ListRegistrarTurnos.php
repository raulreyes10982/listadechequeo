<?php

namespace App\Filament\Resources\RegistrarTurnoResource\Pages;

use App\Filament\Resources\RegistrarTurnoResource;
use App\Models\RegistrarTurno;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\ValidationException;

class ListRegistrarTurnos extends ListRecords
{
    protected static string $resource = RegistrarTurnoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Turno')
                // ✅ Interceptar la ValidationException del modelo
                // y mostrarla como notificación de error visible
                ->using(function (array $data, string $model): RegistrarTurno {
                    try {
                        return $model::create($data);
                    } catch (ValidationException $e) {
                        $mensaje = collect($e->errors())->flatten()->first()
                            ?? 'No se puede registrar el turno por un conflicto de horario.';

                        Notification::make()
                            ->title('⚠️ Conflicto de horario')
                            ->body($mensaje)
                            ->danger()
                            ->persistent()
                            ->send();

                        // Lanzar de nuevo para que Filament no cierre el modal
                        throw $e;
                    }
                }),
        ];
    }
}
