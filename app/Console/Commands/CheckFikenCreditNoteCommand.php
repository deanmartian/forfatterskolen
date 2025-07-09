<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Invoice;
use Illuminate\Console\Command;

class CheckFikenCreditNoteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkfikencreditnote:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check fiken credit note and save the pdf';

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
        CronLog::create(['activity' => 'CheckFikenCreditNote CRON running.']);
        $pageCount = 1;
        for ($page = 0; $page <= $pageCount; $page++) {
            $fikenCreditNoteUrl = config('services.fiken.api_url').'/companies/'.config('services.fiken.company_slug')
                .'/creditNotes?page='.$page.'&pageSize=100';
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer '.config('services.fiken.personal_api_key'),
                'Content-Type: application/json',
            ];

            $ch = curl_init($fikenCreditNoteUrl);
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
            $fikenCreditNotes = json_decode($body);
            $pageCount = $curlHeaders['fiken-api-page-count'][0];
            foreach ($fikenCreditNotes as $fikenCreditNote) {
                if (isset($fikenCreditNote->associatedInvoiceId) && $invoice = Invoice::where('fiken_invoice_id', $fikenCreditNote->associatedInvoiceId)->first()) {
                    $invoice->credit_note_url = $fikenCreditNote->creditNotePdf->downloadUrl;
                    $invoice->save();
                    CronLog::create(['activity' => 'CheckFikenCreditNote CRON updated an invoice with fiken_invoice_id '.$fikenCreditNote->associatedInvoiceId]);
                }
            }
        }

        CronLog::create(['activity' => 'CheckFikenCreditNote CRON done running.']);
        echo 'Done running cron';
    }
}
