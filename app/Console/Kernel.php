<?php

namespace App\Console;

use App\Console\Commands\SyncStoresFromMDM;
use App\Console\Commands\SyncSalesFromTDM;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psy\Command\Command;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SyncStoresFromMDM::class,
        SyncSalesFromTDM::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // sync stores
        $schedule->command('sync:stores:psv:server daily')->daily()
            ->appendOutputTo(base_path() . '/storage/logs/synchronize-stores-MDM-' . Carbon::now()->format('Y-m-d') . '.log');

        // sync sales
//        $schedule->command('sync:sales:psv:server 08.00')->cron('0 8 * * *')
//            ->appendOutputTo(base_path() . '/storage/logs/synchronize-sales-TDM-' . Carbon::now()->format('Y-m-d') . '.log');
//        $schedule->command('sync:sales:psv:server 13.00')->cron('0 13 * * *')
//            ->appendOutputTo(base_path() . '/storage/logs/synchronize-sales-TDM-' . Carbon::now()->format('Y-m-d') . '.log');
//        $schedule->command('sync:sales:psv:server 17.00')->cron('0 17 * * *')
//            ->appendOutputTo(base_path() . '/storage/logs/synchronize-sales-TDM-' . Carbon::now()->format('Y-m-d') . '.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
