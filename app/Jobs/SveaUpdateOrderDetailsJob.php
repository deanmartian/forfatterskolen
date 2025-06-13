<?php

namespace App\Jobs;

use App\Http\FrontendHelpers;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SveaUpdateOrderDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info('inside SVEA update order details job for order_id '.$this->order_id);
        $order = Order::find($this->order_id);
        $sveaOrderDetails = FrontendHelpers::sveaOrderDetails($order->svea_order_id);

        if (isset($sveaOrderDetails['Campaign'])) {
            $order->svea_payment_type_description = $sveaOrderDetails['Campaign']['Description'];
        }

        $fullname = $sveaOrderDetails['BillingAddress']['FullName'] ?: $sveaOrderDetails['ShippingAddress']['FullName'];
        $street = $sveaOrderDetails['BillingAddress']['StreetAddress'] ?: $sveaOrderDetails['ShippingAddress']['StreetAddress'];
        $postalCode = $sveaOrderDetails['BillingAddress']['PostalCode'] ?: $sveaOrderDetails['ShippingAddress']['PostalCode'];
        $city = $sveaOrderDetails['BillingAddress']['City'] ?: $sveaOrderDetails['ShippingAddress']['City'];
        $countryCode = $sveaOrderDetails['BillingAddress']['CountryCode'] ?: $sveaOrderDetails['ShippingAddress']['CountryCode'];

        $order->svea_payment_type = $sveaOrderDetails['PaymentType'];
        $order->svea_fullname = $fullname;
        $order->svea_street = $street;
        $order->svea_postal_code = $postalCode;
        $order->svea_city = $city;
        $order->svea_country_code = $countryCode;
        $order->save();

        Log::info('inside SVEA update order details job svea_payment_type .'.$sveaOrderDetails['PaymentType']);
        Log::info('inside SVEA update order details job svea_fullname .'.$fullname);
        Log::info('inside SVEA update order details job svea_street .'.$street);
        Log::info('inside SVEA update order details job svea_postal_code .'.$postalCode);
        Log::info('inside SVEA update order details job svea_city .'.$city);
        Log::info('inside SVEA update order details job after saving order');
    }
}
