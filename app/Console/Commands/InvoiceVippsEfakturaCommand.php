<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\FikenInvoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvoiceVippsEfakturaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoicevippsefaktura:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create vipps e-faktura for users, would check invoice that would be due in 15 days';

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
        CronLog::create(['activity' => 'InvoiceVippsEfaktura CRON running.']);

        $dueDate = Carbon::today()->addDay(15)->format('Y-m-d');
        $invoices = \DB::table('invoices')
            ->select('invoices.*', 'vipps_phone_number')
            ->leftJoin('users', 'users.id', '=', 'invoices.user_id')
            ->leftJoin('addresses', 'addresses.user_id', '=', 'users.id')
            ->whereDate('fiken_dueDate', $dueDate)
            ->where('fiken_is_paid', '=', 0)
            ->whereNotNull('vipps_phone_number')
            ->get();

        foreach ($invoices as $invoice) {
            $user = User::find($invoice->user_id);
            $fikenInvoice = new FikenInvoice;
            $fikenInvoice->setMobileNumber($invoice->vipps_phone_number);
            $fikenInvoice->setFikenInvoiceId($invoice->fiken_invoice_id);
            $response = $fikenInvoice->vippsEFaktura($user);

            if ($response['code'] == 200) {
                CronLog::create(['activity' => 'InvoiceVippsEfaktura created for invoice id '.$invoice->id.'.']);
            }

        }

        CronLog::create(['activity' => 'InvoiceVippsEfaktura CRON done running.']);
    }
}
