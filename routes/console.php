<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Psy\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('inspire')
//          ->hourly();

Schedule::command('dontavailanything:command')
    ->dailyAt('11:00');
Schedule::command('courseexpiresinamonth:command')
    ->dailyAt('19:00');
Schedule::command('bookreminder:send')
    ->dailyAt('06:00');
Schedule::command('sveadelivery:command')
    ->dailyAt('06:30');
Schedule::command('checkfikeninvoice:command')
    ->dailyAt('17:00');
Schedule::command('checkfikeninvoice:command')
    ->dailyAt('07:30');
Schedule::command('dueinvoicecheck:command')
    ->dailyAt('08:00');
/*Schedule::command('updateinvoice:command')
    ->everyTenMinutes();*/
Schedule::command('webinarpakkeexpiresinaweek:command')
    ->dailyAt('08:00');
Schedule::command('courseemailout:command')
    ->dailyAt('08:00');
Schedule::command('lockfinishedmanuscript:command')
    ->everyThirtyMinutes();
Schedule::command('webinaremailout:command')
    ->dailyAt('09:00');
Schedule::command('gotowebinarreminderday:command')
    ->dailyAt('19:00');
Schedule::command('courseexpirationreminder:command')
    ->dailyAt('08:30');
Schedule::command('checkexpiredcourse:command')
    ->dailyAt('08:30');
Schedule::command('autorenewreminder:command')
    ->dailyAt('07:00');
Schedule::command('checksveaorder:command')
    ->dailyAt('07:30');
Schedule::command('checkfikencontact:command')
    ->dailyAt('07:30');
Schedule::command('invoiceduereminder:command')
    ->dailyAt('08:00');
Schedule::command('delayedemail:command')
    ->dailyAt('08:00');
Schedule::command('invoicevippsefaktura:command')
    ->dailyAt('08:30');
Schedule::command('webinarscheduledregistration:command')
    ->dailyAt('20:30');
Schedule::command('dropbox:refresh-token')->hourly();
/*Schedule::command('updategross:command')
    ->dailyAt('06:00');*/
Schedule::command('freecoursedelayedemail:command')
    ->everyMinute()->withoutOverlapping();
/*Schedule::command('webinarregistranttolearner:command')
    ->yearly();*/
/* Schedule::command('queue:work --tries=5')->everyMinute()->withoutOverlapping(); */
