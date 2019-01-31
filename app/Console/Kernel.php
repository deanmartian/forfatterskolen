<?php

namespace App\Console;

use App\Console\Commands\CourseExpiresInAMonth;
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
        //
        Commands\DontAvailAnythingCommand::class,
        Commands\CourseExpiresInAMonth::class,
        Commands\BookReminder::class,
        Commands\CheckFikenInvoice::class,
        Commands\TestCommand::class,
        Commands\DueInvoiceCheck::class,
        Commands\UpdateInvoice::class,
        Commands\UpdateKidNum::class,
        Commands\WebinarPakkeExpiresInAWeek::class,
        Commands\CourseEmailOut::class,
        Commands\LockFinishedManuscript::class,
        Commands\UpdateGross::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

         $schedule->command('dontavailanything:command')
                  ->dailyAt('11:00');
        $schedule->command('courseexpiresinamonth:command')
                ->dailyAt('19:00');
        $schedule->command('bookreminder:send')
            ->dailyAt('06:00');
        $schedule->command('checkfikeninvoice:command')
            ->dailyAt('07:00');
        $schedule->command('dueinvoicecheck:command')
            ->dailyAt('08:00');
        /*$schedule->command('updateinvoice:command')
            ->everyTenMinutes();*/
        $schedule->command('webinarpakkeexpiresinaweek:command')
            ->dailyAt('08:00');
        $schedule->command('courseemailout:command')
            ->dailyAt('08:00');
        $schedule->command('lockfinishedmanuscript:command')
            ->everyThirtyMinutes();
        /*$schedule->command('updategross:command')
            ->dailyAt('06:00');*/
        $schedule->command('queue:work --daemon')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
