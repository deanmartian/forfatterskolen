<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\PilotReaderBookReading;
use App\PilotReaderBookSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckFikenInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkfikeninvoice:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Fiken and update the invoice';

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
        CronLog::create(['activity' => 'CheckFikenInvoice CRON running.']);
        $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices";
        $username = "cleidoscope@gmail.com";
        $password = "moonfang";
        $headers = [
            'Accept: application/hal+json, application/vnd.error+json',
            'Content-Type: application/hal+json'
        ];

        $ch = curl_init($fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};

        // get all unpaid invoices to reduce process time
        $invoices = Invoice::where('fiken_is_paid','=',0)->get();
        foreach( $invoices as $invoice ) {
            $fiken_balance = 0;
            $status = 0;
            $fikeDueDate = NULL;
            $kid = NULL;
            foreach( $fikenInvoices as $fikenInvoice ) :
                if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
                    $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
                    $status = $sale->paid;
                    $fiken_balance = (double)$fikenInvoice->gross/100;
                    $fikeDueDate = $fikenInvoice->dueDate;
                    $kid = $fikenInvoice->kid;
                    break;
                endif;
            endforeach;
            $invoice->update(['fiken_is_paid' => $status, 'fiken_balance' => $fiken_balance, 'fiken_dueDate' => $fikeDueDate,
                'kid_number' => $kid]);
            CronLog::create(['activity' => 'CheckFikenInvoice CRON updated an invoice with kid_number '.$kid]);
        }

        CronLog::create(['activity' => 'CheckFikenInvoice CRON done running.']);
        return "done checking fiken";
    }
}
