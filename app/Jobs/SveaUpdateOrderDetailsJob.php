<?php

namespace App\Jobs;

use App\Http\FrontendHelpers;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     *
     * @return void
     */
    public function handle() {

        $order = Order::find($this->order_id);
        $sveaOrderDetails = FrontendHelpers::sveaOrderDetails($order->svea_order_id);

        if (isset($sveaOrderDetails['Campaign'])) {
            $order->svea_payment_type_description = $sveaOrderDetails['Campaign']['Description'];
        }

        $order->svea_payment_type = $sveaOrderDetails['PaymentType'];
        $order->save();
    }
}
