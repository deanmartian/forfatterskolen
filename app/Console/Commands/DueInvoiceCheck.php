<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
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
     *
     * @return mixed
     */
    public function handle()
    {
        CronLog::create(['activity' => 'DueInvoiceCheck CRON running.']);

        $from           = 'post@forfatterskolen.no';//$request->from_email;
        $subject        = 'Faktura';
        $dueTomorrow    = Carbon::today()->addDay(1)->format('Y-m-d');

        $invoices = Invoice::whereDate('fiken_dueDate',  $dueTomorrow)
            ->where('fiken_is_paid', '=',0)
            ->get();
        foreach ($invoices as $invoice) {
            $balance            = $invoice->fiken_balance;
            $transactions_sum   = $invoice->transactions->sum('amount');
            $remaining          = $balance - $transactions_sum;

            $to = $invoice->user->email;
            $encode_email = encrypt($to);
            $redirectLink = encrypt(route('learner.invoice', ['filter' => $invoice->id]));
            $link = route('auth.login.emailRedirect',[$encode_email, $redirectLink]);

            $message =  'Du har en faktura som har forfall i morgen <br/>
Pris: '.FrontendHelpers::currencyFormat($remaining).'<br/> Kontonummer: 9015 18 00393 <br/> Kid nummer: '.$invoice->kid_number.' <br/> 
<a href="'.$link.'">Se faktura</a> <br><br> <small>*Merknad: Du må være innlogget for å se fakturaen.</small>';

            AdminHelpers::send_email($subject,
                $from, $to, $message);
            CronLog::create(['activity' => 'DueInvoiceCheck CRON sent email to '.$to]);
        }

        CronLog::create(['activity' => 'DueInvoiceCheck CRON done running.']);
    }
}
