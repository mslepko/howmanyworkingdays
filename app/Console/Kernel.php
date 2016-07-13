<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DownloadCalendar::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

         $schedule->command('calendar:download https://www.gov.uk/bank-holidays/england-and-wales.ics')
             ->daily()
             ->withoutOverlapping()
             ->sendOutputTo(storage_path('logs/calendar-download.log'))
             ->after(function() {
                DownloadCalendar::cleanUp('7 days');
            });
    }
}
