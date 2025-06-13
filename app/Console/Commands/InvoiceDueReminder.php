<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

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

        $email_template = AdminHelpers::emailTemplate('Invoice Due Reminder');
        $from = $email_template->from_email;

        foreach ($invoices as $invoice) {

            $balance = $invoice->fiken_balance;
            $transactions_sum = Transaction::where('invoice_id', $invoice->id)->get()->sum('amount');
            $remaining = $balance - $transactions_sum;
            $user = User::find($invoice->user_id);
            $redirectLink = route('learner.invoice', ['filter' => $invoice->id]);

            if ($user && ! empty($user)) {
                $to = $user->email;
                $emailContent = AdminHelpers::formatEmailContent($email_template->email_content, $to, $user->first_name, $redirectLink);
                $emailContent = str_replace([
                    ':price',
                    ':kid_number',
                ], [
                    FrontendHelpers::currencyFormat($remaining),
                    $invoice->kid_number,
                ], $emailContent);
                dispatch(new AddMailToQueueJob($to, $email_template->subject, $emailContent, $from, null, null,
                    'invoice', $invoice->id));
                CronLog::create(['activity' => 'InvoiceDueReminder CRON sent email to '.$to]);
            }
        }

        CronLog::create(['activity' => 'InvoiceDueReminder CRON done running.']);
    }
}
