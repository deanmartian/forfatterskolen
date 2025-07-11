<?php

namespace App\Console\Commands;

use App\Order;
use Illuminate\Console\Command;

class CheckSveaOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checksveaorder:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the order from svea to get invoice id';

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

        $orders = Order::whereNotNull('svea_order_id')
            ->whereNull('svea_invoice_id')->get();

        foreach ($orders as $order) {

            $base_url = env('SVEA_PROD_URL').'/orders/'.$order->svea_order_id;
            $timestamp = gmdate('Y-m-d H:i');
            $merchantId = env('SVEA_CHECKOUTID');
            $secret = env('SVEA_CHECKOUT_SECRET');

            $token = base64_encode($merchantId.':'.hash('sha512', ''.$secret.$timestamp));
            $header = [];
            $header[] = 'Content-type: application/json';
            $header[] = 'Timestamp: '.$timestamp;
            $header[] = 'Authorization: Svea '.$token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Then, after your curl_exec call:
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $responseHeader = substr($response, 0, $header_size);
            $responseBody = substr($response, $header_size);

            if ($httpcode === 200) {
                $decodeResponse = json_decode($responseBody);
                if ($decodeResponse->Deliveries) {
                    $order->svea_invoice_id = $decodeResponse->Deliveries[0]->InvoiceId;
                    $order->save();
                }
            }
        }

        return 'done';

    }
}
