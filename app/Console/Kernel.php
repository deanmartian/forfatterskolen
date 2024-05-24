<?php

namespace App\Console;

use App\Console\Commands\CourseExpiresInAMonth;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psy\Command\Command;

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
        Commands\CheckFikenCreditNoteCommand::class,
        Commands\CheckFikenInvoice::class,
        Commands\TestCommand::class,
        Commands\DueInvoiceCheck::class,
        Commands\UpdateInvoice::class,
        Commands\UpdateKidNum::class,
        Commands\WebinarPakkeExpiresInAWeek::class,
        Commands\CourseEmailOut::class,
        Commands\LockFinishedManuscript::class,
        Commands\UpdateGross::class,
        Commands\WebinarEmailOutCommand::class,
        Commands\GoToWebinarReminder::class,
        Commands\FreeCourseDelayedEmailCommand::class,
        Commands\CourseExpirationReminder::class,
        Commands\CheckExpiredCourses::class,
        Commands\WebinarRegistrantToLearner::class,
        Commands\AutoRenewReminderCommand::class,
        Commands\CheckSveaOrderCommand::class,
        Commands\InvoiceDueReminder::class,
        Commands\DelayedEmailCommand::class,
        Commands\WebinarScheduledRegistrationCommand::class,
        Commands\InvoiceVippsEfakturaCommand::class,
        Commands\SveaDeliveryCommand::class,
        Commands\CheckFikenContactCommand::class,
        Commands\RefreshDropboxToken::class
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
        $schedule->command('sveadelivery:command')
            ->dailyAt('06:30');
        $schedule->command('checkfikeninvoice:command')
            ->dailyAt('17:00');
        $schedule->command('checkfikeninvoice:command')
            ->dailyAt('07:30');
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
        $schedule->command('webinaremailout:command')
            ->dailyAt('09:00');
        $schedule->command('gotowebinarreminderday:command')
            ->dailyAt('19:00');
        $schedule->command('courseexpirationreminder:command')
            ->dailyAt('08:30');
        $schedule->command('checkexpiredcourse:command')
            ->dailyAt('08:30');
        $schedule->command('autorenewreminder:command')
            ->dailyAt('07:00');
        $schedule->command('checksveaorder:command')
            ->dailyAt('07:30');
        $schedule->command('checkfikencontact:command')
            ->dailyAt('07:30');
        $schedule->command('invoiceduereminder:command')
            ->dailyAt('08:00');
        $schedule->command('delayedemail:command')
            ->dailyAt('08:00');
        $schedule->command('invoicevippsefaktura:command')
            ->dailyAt('08:30');
        $schedule->command('webinarscheduledregistration:command')
            ->dailyAt('20:30');
        $schedule->command('dropbox:refresh-token')->everyThirtyMinutes();
        /*$schedule->command('updategross:command')
            ->dailyAt('06:00');*/
        $schedule->command('freecoursedelayedemail:command')
            ->everyMinute()->withoutOverlapping();
        /*$schedule->command('webinarregistranttolearner:command')
            ->yearly();*/
        /*$schedule->command('queue:work --tries=5')->everyMinute()->withoutOverlapping();*/
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
