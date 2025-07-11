<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\FrontendHelpers;
use App\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
     */
    public function handle()
    {
        CronLog::create(['activity' => 'CheckFikenInvoice CRON running.']);
        Log::info('checkfikeninvoice:command running');
        $pageCount = 1;
        // LIVE:forfatterskolen-as DEMO: fiken-demo-glede-og-bil-as2
        $company = 'forfatterskolen-as';

        for ($page = 0; $page <= $pageCount; $page++) {
            $fikenInvoiceUrl = 'https://api.fiken.no/api/v2/companies/'.$company.'/invoices?page='.$page
                .'&pageSize=100';
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.config('services.fiken.personal_api_key'),
                'Content-Type: Application/json',
            ];

            $ch = curl_init($fikenInvoiceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            // this function is called by curl for each header received
            $curlHeaders = [];
            curl_setopt($ch, CURLOPT_HEADERFUNCTION,
                function ($curl, $header) use (&$curlHeaders) {
                    $len = strlen($header);
                    $header = explode(':', $header, 2);
                    if (count($header) < 2) { // ignore invalid headers
                        return $len;
                    }

                    $curlHeaders[strtolower(trim($header[0]))][] = trim($header[1]);

                    return $len;
                }
            );

            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = substr($response, $header_size);
            $fikenInvoices = json_decode($body);
            $pageCount = $curlHeaders['fiken-api-page-count'][0];

            foreach ($fikenInvoices as $fikenInvoice) {
                $fiken_balance = isset($fikenInvoices->sale->outstandingBalance)
                    ? $fikenInvoices->sale->outstandingBalance : $fikenInvoice->gross;
                $fiken_balance = (float) $fiken_balance / 100;
                $status = $fikenInvoice->sale->settled ? 1 : 0;
                $fikenDueDate = $fikenInvoice->dueDate;
                $kid = isset($fikenInvoice->kid) ? $fikenInvoice->kid : null;
                $gross = $fikenInvoice->gross;
                $fikenIssueDate = $fikenInvoice->issueDate;

                $invoice = Invoice::where('invoice_number', $fikenInvoice->invoiceNumber)
                    ->whereIn('fiken_is_paid', [0])->first();

                if ($invoice) {
                    Log::info('updated invoice id = '.$invoice->id);
                    $invoice->fiken_is_paid = $fikenInvoice->associatedCreditNotes ? 3 : $status;
                    $invoice->fiken_balance = $fiken_balance;
                    $invoice->fiken_dueDate = $fikenDueDate;
                    $invoice->kid_number = $kid;
                    $invoice->fiken_issueDate = $fikenIssueDate;
                    $invoice->gross = $gross;
                    $invoice->fiken_invoice_id = $fikenInvoice->invoiceId;
                    $invoice->save();
                    CronLog::create(['activity' => 'CheckFikenInvoice CRON updated an invoice with kid_number '.$kid]);
                }
            }

        }

        CronLog::create(['activity' => 'CheckFikenInvoice CRON done running.']);
        echo 'done checking fiken';
        Log::info('checkfikeninvoice:command done running');

    }

    public function handleOrig()
    {
        CronLog::create(['activity' => 'CheckFikenInvoice CRON running.']);
        $fikenInvoices = 'https://fiken.no/api/v1/companies/forfatterskolen-as/invoices';
        $username = 'cleidoscope@gmail.com';
        $password = 'moonfang';
        $headers = [
            'Accept: application/hal+json, application/vnd.error+json',
            'Content-Type: application/hal+json',
        ];

        $ch = curl_init($fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        print_r($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};

        // get all unpaid invoices to reduce process time
        $invoices = Invoice::whereIn('fiken_is_paid', [0, 2])->get();
        foreach ($invoices as $invoice) {
            $fiken_balance = 0;
            $status = 0;
            $fikeDueDate = null;
            $kid = null;
            $gross = null;
            $fikenIssueDate = null;
            foreach ($fikenInvoices as $fikenInvoice) {
                if ($invoice->fiken_url == $fikenInvoice->_links->alternate->href) {
                    $sale = FrontendHelpers::FikenConnect($fikenInvoice->sale);
                    $status = $sale->paid;
                    $fiken_balance = (float) $fikenInvoice->gross / 100;
                    $fikeDueDate = $fikenInvoice->dueDate;
                    $kid = $fikenInvoice->kid;
                    $gross = $fikenInvoice->gross;
                    $fikenIssueDate = $fikenInvoice->issueDate;
                    break;
                }
            }
            $status = $status == 0 ? $invoice->fiken_is_paid : $status;
            $invoice->update(['fiken_is_paid' => $status, 'fiken_balance' => $fiken_balance, 'fiken_dueDate' => $fikeDueDate,
                'kid_number' => $kid, 'fiken_issueDate' => $fikenIssueDate, 'gross' => $gross]);
            CronLog::create(['activity' => 'CheckFikenInvoice CRON updated an invoice with kid_number '.$kid]);
        }

        CronLog::create(['activity' => 'CheckFikenInvoice CRON done running.']);

        return 'done checking fiken';
    }
}
