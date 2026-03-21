<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Mail\InvoiceReminderMail;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class InvoiceDueReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoiceduereminder:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoices that is due on 14 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        CronLog::create(['activity' => 'InvoiceDueReminder CRON running.']);

        $dueDate = Carbon::today()->addDay(14)->format('Y-m-d');
        /*$invoices = Invoice::whereDate('fiken_dueDate',  $dueDate)
            ->where('fiken_is_paid', '=',0)
            ->get();*/

        $invoices = \DB::table('invoices')
            ->select('invoices.*', 'vipps_phone_number')
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')
            ->leftJoin('addresses', 'addresses.user_id', '=', 'users.id')
            ->whereDate('fiken_dueDate', $dueDate)
            ->where('fiken_is_paid', '=', 0)
            ->whereNull('vipps_phone_number')
            ->whereNull('users.deleted_at')
            ->get();

        foreach ($invoices as $invoice) {

            $balance = $invoice->fiken_balance;
            $transactions_sum = Transaction::where('invoice_id', $invoice->id)->get()->sum('amount');
            $remaining = $balance - $transactions_sum;
            $user = User::find($invoice->user_id);

            if ($user && ! empty($user) && $user->wantsNotification('invoice_due_reminder')) {
                Mail::to($user->email)->queue(new InvoiceReminderMail([
                    'type' => 'reminder',
                    'subject' => 'Påminnelse: faktura forfaller om 14 dager',
                    'email' => $user->email,
                    'firstName' => $user->first_name,
                    'amount' => FrontendHelpers::currencyFormat($remaining),
                    'dueDate' => Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y'),
                    'kidNumber' => $invoice->kid_number,
                    'payUrl' => route('learner.invoice', ['filter' => $invoice->id]),
                ]));
                CronLog::create(['activity' => 'InvoiceDueReminder CRON sent email to '.$user->email]);
            }
        }

        CronLog::create(['activity' => 'InvoiceDueReminder CRON done running.']);
    }
}
