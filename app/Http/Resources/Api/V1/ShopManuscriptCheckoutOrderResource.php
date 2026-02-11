<?php

namespace App\Http\Resources\Api\V1;

use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopManuscriptCheckoutOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Order $order */
        $order = $this->resource;
        $status = $this->resolveStatus($order);

        return [
            'order_id' => $order->id,
            'status' => $status,
            'amount' => (float) (($order->price + $order->additional) - $order->discount),
            'currency' => 'NOK',
            'payment_provider' => $this->provider($order),
            'payment_url' => $order->getAttribute('checkout_payment_url'),
            'message' => $order->getAttribute('checkout_message') ?: $this->defaultMessage($status),
        ];
    }

    private function resolveStatus(Order $order): string
    {
        if ((int) ($order->is_order_withdrawn ?? 0) === 1) {
            return 'cancelled';
        }

        if ((int) $order->is_processed === 1) {
            return 'paid';
        }

        return 'pending';
    }

    private function provider(Order $order): string
    {
        $mode = strtolower((string) optional($order->paymentMode)->mode);

        if ($mode === 'vipps') {
            return 'vipps';
        }

        if ($mode === 'svea') {
            return 'svea';
        }

        if ((int) ($order->payment_mode_id ?? 0) === 3 || $mode === 'faktura') {
            return 'svea';
        }

        return 'manual';
    }

    private function defaultMessage(string $status): string
    {
        return match ($status) {
            'paid' => 'Payment captured and access granted.',
            'cancelled' => 'Order cancelled.',
            'failed' => 'Payment failed.',
            default => 'Checkout order created and awaiting payment.',
        };
    }
}
