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
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:refresh-mpesa-access-tokens')->everyThirtyMinutes();
        $schedule->command('app:send-pledges-notifications')->dailyAt('09:00');
        $schedule->command('app:reset-pledge-mpesa-stk-count')->daily();
        $schedule->command('app:generate-account-donations-stats')->dailyAt('00:05');
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
