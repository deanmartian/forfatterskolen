<?php

namespace App\Console\Commands;

use App\Invoice;
use Illuminate\Console\Command;

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
     *
     * @return mixed
     */
    public function handle()
    {
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


        $invoices = Invoice::whereNull('kid_number')
            ->orWhereNull('invoice_number')
            ->get();
        foreach( $invoices as $invoice ) {
            $kid            = NULL;
            $invoice_number = NULL;
            $issueDate      = NULL;
            foreach( $fikenInvoices as $fikenInvoice ) :
                if( $invoice->fiken_url == $fikenInvoice->_links->alternate->href ) :
                    $kid = isset($fikenInvoice->kid) ? $fikenInvoice->kid : NULL;
                    $invoice_number = isset($fikenInvoice->invoiceNumber) ? $fikenInvoice->invoiceNumber : NULL;
                    $issueDate = isset($fikenInvoice->issueDate) ? $fikenInvoice->issueDate : NULL;
                    break;
                endif;
            endforeach;
            $invoice->update(['kid_number' => $kid, 'invoice_number' => $invoice_number, 'fiken_issueDate' => $issueDate]);
        }

        return "done checking fiken";
    }
}