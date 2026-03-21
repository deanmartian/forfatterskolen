<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Mail\InvoiceReminderMail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DueInvoiceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dueinvoicecheck:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoices that is due tomorrow';

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
        CronLog::create(['activity' => 'DueInvoiceCheck CRON running.']);
        $dueTomorrow = Carbon::today()->addDay(1)->format('Y-m-d');

        /*$invoices = Invoice::whereDate('fiken_dueDate',  $dueTomorrow)
            ->where('fiken_is_paid', '=',0)
            ->get();*/

        $invoices = \DB::table('invoices')
            ->select('invoices.*', 'vipps_phone_number',
                \DB::raw('SUM(transactions.amount) as transaction_amount'), 'users.first_name as user_first_name',
                'users.email as user_email')
            ->leftJoin('transactions', 'invoices.id', '=', 'transactions.invoice_id')
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')
            ->leftJoin('addresses', 'addresses.user_id', '=', 'users.id')
            ->whereDate('fiken_dueDate', $dueTomorrow)
            ->where('fiken_is_paid', '=', 0)
            ->whereNull('vipps_phone_number')
            ->get();

        foreach ($invoices as $invoice) {
            if ($invoice->id) {
                $balance = $invoice->fiken_balance;
                $transactions_sum = $invoice->transaction_amount;
                $remaining = $balance - $transactions_sum;

                $user = User::find($invoice->user_id);
                if (!$user) continue;

                Mail::to($user->email)->queue(new InvoiceReminderMail([
                    'type' => 'overdue',
                    'subject' => 'Fakturaen din forfaller i morgen',
                    'email' => $user->email,
                    'firstName' => $user->first_name,
                    'amount' => FrontendHelpers::currencyFormat($remaining),
                    'dueDate' => Carbon::parse($invoice->fiken_dueDate)->format('d.m.Y'),
                    'kidNumber' => $invoice->kid_number,
                    'payUrl' => route('learner.invoice', ['filter' => $invoice->id]),
                ]));
                CronLog::create(['activity' => 'DueInvoiceCheck CRON sent email to '.$user->email]);
            }
        }

        CronLog::create(['activity' => 'DueInvoiceCheck CRON done running.']);
    }
}
