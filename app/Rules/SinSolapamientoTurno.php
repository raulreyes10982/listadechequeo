<?php

namespace App\Rules;

use App\Models\RegistrarTurno;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regla de validación de solapamiento de turnos.
 *
 * Verifica DOS condiciones de conflicto:
 *
 * 1. MISMO PUESTO — no puede haber otro guardia en este puesto en ese horario.
 * 2. MISMO GUARDIA — el guardia no puede estar en otro puesto en ese horario.
 *
 * Condición matemática de solapamiento entre [A,B] y [C,D]:
 *   Se solapan si: A < D  AND  C < B
 */
class SinSolapamientoTurno implements ValidationRule
{
    public function __construct(
        private readonly ?string $fecha,
        private readonly ?string $horaInicio,
        private readonly ?string $horaFin,
        private readonly ?int    $puestoId,
        private readonly ?int    $colaboradorId,
        private readonly ?int    $excludeId = null // ID del turno actual (al editar)
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->fecha || ! $this->horaInicio || ! $this->horaFin) {
            return;
        }

        $baseQuery = RegistrarTurno::query()
            ->whereDate('fecha', $this->fecha)
            ->when($this->excludeId, fn ($q) => $q->where('id', '!=', $this->excludeId))
            // Condición de solapamiento: inicio_existente < fin_nuevo AND fin_existente > inicio_nuevo
            ->where('hora_inicio', '<', $this->horaFin)
            ->where('hora_fin',    '>', $this->horaInicio);

        // ── REGLA 1: mismo puesto ─────────────────────────────────────────
        if ($this->puestoId) {
            $conflictoPuesto = (clone $baseQuery)
                ->where('puesto_seguridad_id', $this->puestoId)
                ->with('colaborador')
                ->first();

            if ($conflictoPuesto) {
                $nombre = trim(
                    ($conflictoPuesto->colaborador->nombre   ?? '') . ' ' .
                    ($conflictoPuesto->colaborador->apellido ?? '')
                );
                $ini = Carbon::parse($conflictoPuesto->hora_inicio)->format('H:i');
                $fin = Carbon::parse($conflictoPuesto->hora_fin)->format('H:i');

                $fail(
                    "⚠️ Este puesto ya tiene un guardia asignado en ese horario: " .
                    "{$nombre} ({$ini} — {$fin}). " .
                    "Elige otro horario u otro puesto."
                );
                return; // Un error es suficiente
            }
        }

        // ── REGLA 2: mismo guardia en otro puesto ─────────────────────────
        if ($this->colaboradorId) {
            $conflictoGuardia = (clone $baseQuery)
                ->where('colaborador_id', $this->colaboradorId)
                ->with('puestoSeguridad')
                ->first();

            if ($conflictoGuardia) {
                $puesto = $conflictoGuardia->puestoSeguridad->puesto ?? 'otro puesto';
                $codigo = $conflictoGuardia->puestoSeguridad->codigo ?? '';
                $ini    = Carbon::parse($conflictoGuardia->hora_inicio)->format('H:i');
                $fin    = Carbon::parse($conflictoGuardia->hora_fin)->format('H:i');

                $fail(
                    "⚠️ Este guardia ya tiene un turno asignado en ese horario: " .
                    "{$codigo} {$puesto} ({$ini} — {$fin}). " .
                    "Un guardia no puede estar en dos puestos al mismo tiempo."
                );
            }
        }
    }
}
