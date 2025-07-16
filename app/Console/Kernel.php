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
        $schedule->command('dontavailanything:command')->dailyAt('11:00');
        $schedule->command('courseexpiresinamonth:command')->dailyAt('19:00');
        $schedule->command('bookreminder:send')->dailyAt('06:00');
        $schedule->command('sveadelivery:command')->dailyAt('06:30');
        $schedule->command('checkfikeninvoice:command')->dailyAt('17:00');
        $schedule->command('checkfikeninvoice:command')->dailyAt('07:30');
        $schedule->command('dueinvoicecheck:command')->dailyAt('08:00');
        $schedule->command('webinarpakkeexpiresinaweek:command')->dailyAt('08:00');
        $schedule->command('courseemailout:command')->dailyAt('08:00');
        $schedule->command('lockfinishedmanuscript:command')->everyThirtyMinutes();
        $schedule->command('webinaremailout:command')->dailyAt('09:00');
        $schedule->command('gotowebinarreminderday:command')->dailyAt('19:00');
        $schedule->command('courseexpirationreminder:command')->dailyAt('08:30');
        $schedule->command('checkexpiredcourse:command')->dailyAt('08:30');
        $schedule->command('autorenewreminder:command')->dailyAt('07:00');
        $schedule->command('checksveaorder:command')->dailyAt('07:30');
        $schedule->command('checkfikencontact:command')->dailyAt('07:30');
        $schedule->command('invoiceduereminder:command')->dailyAt('08:00');
        $schedule->command('delayedemail:command')->dailyAt('08:00');
        $schedule->command('invoicevippsefaktura:command')->dailyAt('08:30');
        $schedule->command('webinarscheduledregistration:command')->dailyAt('20:30');
        $schedule->command('dropbox:refresh-token')->hourly();
        $schedule->command('freecoursedelayedemail:command')->everyMinute()->withoutOverlapping();
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
