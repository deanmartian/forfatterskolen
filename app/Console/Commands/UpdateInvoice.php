<?php

namespace App\Console\Commands;

use App\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateinvoice:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the kid/invoice number of invoices';

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
        $pageCount = 1;
        // LIVE:forfatterskolen-as DEMO: fiken-demo-glede-og-bil-as2
        $company = 'forfatterskolen-as';
        Log::info('updateinvoice:command running');
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

                $invoice = Invoice::where('invoice_number', $fikenInvoice->invoiceNumber)->first();

                if ($invoice) {
                    Log::info('updated invoice id = '.$invoice->id);
                    $invoice->fiken_invoice_id = $fikenInvoice->invoiceId;
                    $invoice->save();
                }

            }
        }

        echo 'done update invoice';
        Log::info('updateinvoice:command done running');

    }

    public function handleOrig()
    {
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
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};

        $invoices = Invoice::whereNull('kid_number')
            ->orWhereNull('invoice_number')
            ->get();
        foreach ($invoices as $invoice) {
            $kid = null;
            $invoice_number = null;
            $issueDate = null;
            foreach ($fikenInvoices as $fikenInvoice) {
                if ($invoice->fiken_url == $fikenInvoice->_links->alternate->href) {
                    $kid = isset($fikenInvoice->kid) ? $fikenInvoice->kid : null;
                    $invoice_number = isset($fikenInvoice->invoiceNumber) ? $fikenInvoice->invoiceNumber : null;
                    $issueDate = isset($fikenInvoice->issueDate) ? $fikenInvoice->issueDate : null;
                    break;
                }
            }
            $invoice->update(['kid_number' => $kid, 'invoice_number' => $invoice_number, 'fiken_issueDate' => $issueDate]);
        }

        return 'done checking fiken';
    }
}
