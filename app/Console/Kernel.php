<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// ✅ Add your command here
use App\Console\Commands\CheckDocumentExpiries;

class Kernel extends ConsoleKernel
{
    /**
     * Register the Artisan commands.
     *
     * @var array
     */
    protected $commands = [
        CheckDocumentExpiries::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ✅ Schedule the expiry check to run daily
        $schedule->command('documents:check-expiries')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
