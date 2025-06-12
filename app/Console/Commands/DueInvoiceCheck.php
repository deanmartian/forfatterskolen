<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        $email_template = AdminHelpers::emailTemplate('Due Invoice Check');
        $from = $email_template->from_email;
        $subject = $email_template->subject;

        foreach ($invoices as $invoice) {
            if ($invoice->id) {
                $balance = $invoice->fiken_balance;
                $transactions_sum = $invoice->transaction_amount; // $invoice->transactions->sum('amount');
                $remaining = $balance - $transactions_sum;
                // $user               = $invoice->user;

                $to = $invoice->user_email; // $invoice->user->email;
                $redirectLink = route('learner.invoice', ['filter' => $invoice->id]);

                $emailContent = AdminHelpers::formatEmailContent($email_template->email_content, $to, $invoice->user_first_name, $redirectLink);
                $emailContent = str_replace([
                    ':price',
                    ':kid_number',
                ], [
                    FrontendHelpers::currencyFormat($remaining),
                    $invoice->kid_number,
                ], $emailContent);

                // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                dispatch(new AddMailToQueueJob($to, $subject, $emailContent, $from, null, null,
                    'invoice', $invoice->id));
                CronLog::create(['activity' => 'DueInvoiceCheck CRON sent email to '.$to]);
            }
        }

        CronLog::create(['activity' => 'DueInvoiceCheck CRON done running.']);
    }
}
