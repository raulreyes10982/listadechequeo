<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PuestoSeguridad;
use Carbon\Carbon;

class RotatePuestoQrs extends Command
{
    protected $signature = 'puestos:rotate-qrs';
    protected $description = 'Renueva automáticamente los QR de los puestos de seguridad';

    public function handle()
    {
        $now = Carbon::now();

        $puestos = PuestoSeguridad::whereNull('qr_expira')
            ->orWhere('qr_expira', '<=', $now)
            ->get();

        if ($puestos->isEmpty()) {
            $this->info('No hay puestos que necesiten renovación de QR.');
            return Command::SUCCESS;
        }

        foreach ($puestos as $puesto) {
            // ✅ CORRECCIÓN 1: forzar la regeneración limpiando el token anterior
            // para que generarQrSiNecesario() detecte que debe crear uno nuevo
            $puesto->qr_token  = null;
            $puesto->qr_expira = null;

            // ✅ CORRECCIÓN 2: usar el método del modelo que genera un token
            // criptográficamente seguro (SHA256 + UUID + app.key + microtime)
            // en lugar de Str::random(40) que es predecible
            $puesto->generarQrSiNecesario();

            // ✅ CORRECCIÓN 3: el campo es "puesto", no "nombre"
            $this->info("QR renovado para puesto: {$puesto->codigo} ({$puesto->puesto})");
        }

        $this->info("Total renovados: {$puestos->count()}");

        return Command::SUCCESS;
    }
}
