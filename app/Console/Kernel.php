<?php

namespace App\Console;

use App\Console\Commands\ExtractItemsCommand;
use App\Console\Commands\ProcessItemsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\CheckBrokenUrls;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These commands may be run in a single run of the scheduler.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(ExtractItemsCommand::class)
            ->monthly()
            ->then(function () {
                Artisan::call(ProcessItemsCommand::class);
            });

        $schedule->job(new CheckBrokenUrls)->monthly();
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
