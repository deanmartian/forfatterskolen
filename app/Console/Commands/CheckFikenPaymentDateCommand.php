<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Invoice;
use Illuminate\Console\Command;
use Log;

class CheckFikenPaymentDateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkfikenpaymentdate:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check payment date for paid fiken invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CronLog::create(['activity' => 'CheckFikenPaymentDate CRON running.']);
        $pageCount = 1;
        // LIVE:forfatterskolen-as DEMO: fiken-demo-glede-og-bil-as2
        $company = 'forfatterskolen-as';

        for ($page = 0; $page <= $pageCount; $page++) {
             $fikenInvoiceUrl = 'https://api.fiken.no/api/v2/companies/'.$company.'/invoices?page='.$page
                .'&pageSize=100&settled=1';
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
                $invoice = Invoice::where('invoice_number', $fikenInvoice->invoiceNumber)
                    ->where('fiken_is_paid', 1)
                    ->whereNull('fiken_sale_payment_date')
                    ->first();

                if (!$invoice) {
                    continue;
                }

                // Safe lookup: returns null if any segment is missing/empty
                $paymentDate = data_get($fikenInvoice, 'sale.salePayments.0.date');

                if ($paymentDate) {
                    Log::info('updated invoice id = ' . $invoice->id);

                    $invoice->fiken_sale_payment_date = $paymentDate;
                    $invoice->save();

                    CronLog::create([
                        'activity' => 'CheckFikenPaymentDate CRON updated an invoice with invoice_number ' . $fikenInvoice->invoiceNumber
                    ]);
                }
            }
        }

        CronLog::create(['activity' => 'CheckFikenPaymentDate CRON done running.']);
    }
}
