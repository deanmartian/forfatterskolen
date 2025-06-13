<?php

namespace App\Console\Commands;

use App\Invoice;
use Illuminate\Console\Command;

class UpdateKidNum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatekidnum:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the kid number of invoices';

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
    public function handle(): mixed
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

        // get all unpaid invoices to reduce process time
        $invoices = Invoice::get();
        foreach ($invoices as $invoice) {
            $kid = null;
            foreach ($fikenInvoices as $fikenInvoice) {
                if ($invoice->fiken_url == $fikenInvoice->_links->alternate->href) {
                    $kid = isset($fikenInvoice->kid) ? $fikenInvoice->kid : null;
                    break;
                }
            }
            $invoice->update(['kid_number' => $kid]);
        }

        return 'done checking fiken';
    }
}
