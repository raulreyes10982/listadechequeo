<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permiso;

class VerificacionesBackfill extends Command
{
    protected $signature = 'verificaciones:backfill';
    protected $description = 'Crea verificaciones diarias faltantes para todos los permisos y trabajadores';

    public function handle(): int
    {
        $this->info('Generando verificaciones faltantes...');
        Permiso::with('trabajadores')->chunk(200, function ($permisos) {
            foreach ($permisos as $permiso) {
                foreach ($permiso->trabajadores as $t) {
                    $permiso->generarVerificacionesParaTrabajador($t);
                }
            }
        });
        $this->info('Listo ✅');
        return self::SUCCESS;
    }
}
