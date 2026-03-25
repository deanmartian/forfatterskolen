<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\BookOrder;
use App\Repositories\VippsRepository;
use App\StorageInventory;
use App\ProjectBookSale;
use Illuminate\Http\Request;

class ShopPaymentController extends Controller
{
    /**
     * Hent VippsRepository med Indiemoon-nøkler (KUN bokkjøp/selvpublisering)
     */
    private function indiemoonVipps(): VippsRepository
    {
        // Midlertidig sett Indiemoon-nøkler i config
        config([
            'services.vipps.client_id' => config('shop.vipps.client_id'),
            'services.vipps.client_secret' => config('shop.vipps.client_secret'),
            'services.vipps.subscription_key' => config('shop.vipps.subscription_key'),
            'services.vipps.merchant_serial_number' => config('shop.vipps.msn'),
        ]);

        return new VippsRepository();
    }

    public function vipps($orderNumber)
    {
        $order = BookOrder::where('order_number', $orderNumber)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        $vipps = $this->indiemoonVipps();

        $reference = 'SHOP-' . $order->id;
        $result = $vipps->initiatePayment(
            $order->total * 100, // Vipps bruker øre
            $reference,
            $order->customer_phone ?? '',
            "Indiemoon bestilling {$order->order_number}",
            config('shop.frontend_url') . '/bestilling/' . $order->order_number . '/bekreftelse'
        );

        if (!$result || empty($result['url'])) {
            return response()->json(['error' => 'Kunne ikke starte Vipps-betaling.'], 500);
        }

        $order->update([
            'payment_method' => 'vipps',
            'payment_reference' => $reference,
        ]);

        return response()->json(['redirect_url' => $result['url']]);
    }

    public function vippsWebhook(Request $request)
    {
        $reference = $request->input('reference') ?? $request->input('orderId');
        if (!$reference || !str_starts_with($reference, 'SHOP-')) {
            return response()->json(['status' => 'ignored']);
        }

        $orderId = (int) str_replace('SHOP-', '', $reference);
        $order = BookOrder::find($orderId);

        if (!$order || $order->isPaid()) {
            return response()->json(['status' => 'already_processed']);
        }

        // Sjekk Vipps-status
        $vipps = $this->indiemoonVipps();
        $details = $vipps->getPaymentDetails($reference);

        $captured = collect($details['transactionLogHistory'] ?? [])
            ->contains(fn($t) => $t['operation'] === 'CAPTURE');

        if ($captured) {
            $this->completeOrder($order);
        }

        return response()->json(['status' => 'ok']);
    }

    public function checkStatus($orderNumber)
    {
        $order = BookOrder::where('order_number', $orderNumber)->firstOrFail();

        if ($order->isPaid()) {
            return response()->json(['paid' => true]);
        }

        if ($order->payment_reference && str_starts_with($order->payment_reference, 'SHOP-')) {
            $vipps = $this->indiemoonVipps();
            $details = $vipps->getPaymentDetails($order->payment_reference);

            $captured = collect($details['transactionLogHistory'] ?? [])
                ->contains(fn($t) => $t['operation'] === 'CAPTURE');

            if ($captured) {
                $this->completeOrder($order);
                return response()->json(['paid' => true]);
            }
        }

        return response()->json(['paid' => false]);
    }

    private function completeOrder(BookOrder $order): void
    {
        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // Trekk lager og registrer salg for fysiske bøker
        foreach ($order->items as $item) {
            if ($item['format'] !== 'ebook') {
                // Trekk lager
                StorageInventory::where('project_book_id', $item['book_id'])
                    ->decrement('balance', $item['quantity']);

                // Registrer salg
                ProjectBookSale::create([
                    'project_book_id' => $item['book_id'],
                    'invoice_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'quantity' => $item['quantity'],
                    'full_price' => $item['price'],
                    'discount' => 0,
                    'amount' => $item['line_total'],
                    'date' => now()->format('Y-m-d'),
                ]);
            }
        }

        // TODO: Send ordrebekreftelse-mail
        // TODO: Opprett Fiken-faktura
    }
}
