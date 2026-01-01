<?php

namespace App\Console;

use App\Console\Commands\CloseInventoryPeriod;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Close inventory period automatically at 11:59 PM on the last day of each month
        $schedule->command(CloseInventoryPeriod::class)
            ->monthlyOn(date('t'), '23:59') // Last day of month at 11:59 PM
            ->withoutOverlapping()
            ->onFailure(function () {
                \Log::error('Inventory period closing failed during scheduled run');
            })
            ->onSuccess(function () {
                \Log::info('Inventory period closed successfully');
            });
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
