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
        $metadata = is_array($order->shopManuscriptOrder?->description)
            ? $order->shopManuscriptOrder->description
            : (json_decode((string) $order->shopManuscriptOrder?->description, true) ?: []);

        $checkout = $metadata['checkout'] ?? [];
        $status = $checkout['status'] ?? $this->resolveStatus($order);

        return [
            'order_id' => $order->id,
            'status' => $status,
            'amount' => (float) ($order->price - $order->discount),
            'currency' => 'NOK',
            'payment_provider' => $checkout['payment_provider'] ?? strtolower((string) optional($order->paymentMode)->mode ?: 'manual'),
            'payment_url' => $checkout['payment_url'] ?? null,
            'message' => $checkout['message'] ?? $this->defaultMessage($status),
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
