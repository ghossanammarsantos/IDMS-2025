<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GenerateCodeco::class,
        \App\Console\Commands\DispatchCodeco::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // EXISTING: laporan survey in
        $schedule->command('report:surveyin:send')
            ->cron('0 */3 * * *')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->onOneServer();

        // NEW: generate CODECO Gate IN tiap 3 jam
        $schedule->command('edi:codeco:generate --event=IN')
            ->cron('0 */3 * * *')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->onOneServer();

        // NEW: generate CODECO Gate OUT tiap 3 jam
        $schedule->command('edi:codeco:generate --event=OUT')
            ->cron('0 */3 * * *')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
