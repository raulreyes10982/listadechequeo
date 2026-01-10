<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PuestoSeguridad;
use Illuminate\Support\Str;
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

        foreach ($puestos as $puesto) {
            $puesto->qr_token = Str::random(40);
            $puesto->qr_expira = $now->copy()->addDays(30);
            $puesto->save();

            $this->info("QR renovado para puesto: {$puesto->codigo} ({$puesto->nombre})");
        }

        return Command::SUCCESS;
    }
}
