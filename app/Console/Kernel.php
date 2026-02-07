<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Limpiar reservas de stock expiradas cada 5 minutos
        $schedule->command('stock:clear-expired-reservations')->everyFiveMinutes();
        
        // Limpiar tokens expirados diariamente
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        
        // Limpiar logs de auditoría antiguos mensualmente
        $schedule->command('audit:prune --days=90')->monthly();
        
        // Backup de base de datos diario
        // $schedule->command('backup:run')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
