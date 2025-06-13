<?php

namespace App\Console\Commands;

use App\CronLog;
use App\Http\FrontendHelpers;
use App\Order;
use Illuminate\Console\Command;

class SveaDeliveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sveadelivery:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the delivery id of the svea order';

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
    public function handle(): void
    {
        $orders = Order::whereNotNull('svea_order_id')
            ->whereNull('svea_delivery_id')
            ->get();

        CronLog::create(['activity' => 'SveaDelivery CRON running.']);
        foreach ($orders as $order) {
            $sveaOrderDetails = FrontendHelpers::sveaOrderDetails($order->svea_order_id);
            // check if delivered and has available actions allowed
            if (is_array($sveaOrderDetails) && $sveaOrderDetails['Deliveries'] && count($sveaOrderDetails['Deliveries'][0]['Actions'])) {
                $order->svea_delivery_id = $sveaOrderDetails['Deliveries'][0]['Id'];
                $order->save();
                CronLog::create(['activity' => 'SveaDelivery CRON updated order '.$order->id.'.']);
            }
        }
        echo 'Done';
        CronLog::create(['activity' => 'SveaDelivery CRON done running.']);
    }
}
