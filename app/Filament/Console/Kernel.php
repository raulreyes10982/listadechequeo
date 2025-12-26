<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected $commands = [
        // Aquí van tus comandos
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Crea los registros que falten (hoy y futuro), idempotente gracias al unique
        $schedule->command('verificaciones:backfill')->dailyAt('00:05');
        $schedule->command('puestos:rotate-qrs')->dailyAt('00:10');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    
}
