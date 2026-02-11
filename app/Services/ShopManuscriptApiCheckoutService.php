<?php

namespace App\Services;

use App\Order;
use App\OrderShopManuscript;
use App\PaymentMode;
use App\PaymentPlan;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\User;
use Illuminate\Support\Facades\DB;

class ShopManuscriptApiCheckoutService
{
    public function createOrder(User $user, ShopManuscript $shopManuscript, string $idempotencyKey): Order
    {
        return DB::transaction(function () use ($user, $shopManuscript, $idempotencyKey) {
            $existing = Order::query()
                ->with(['shopManuscriptOrder', 'paymentMode'])
                ->where('user_id', $user->id)
                ->where('type', Order::MANUSCRIPT_TYPE)
                ->where('item_id', $shopManuscript->id)
                ->where('is_processed', 0)
                ->whereHas('shopManuscriptOrder', function ($query) use ($idempotencyKey) {
                    $query->where('genre', $this->idempotencyGenre($idempotencyKey));
                })
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $this->assertCanPurchase($user, $shopManuscript);

            $paymentPlan = PaymentPlan::query()->where('division', 1)->orderBy('id')->first();
            $paymentMode = PaymentMode::query()->where('mode', 'Vipps')->first();

            $provider = $paymentMode ? 'vipps' : 'manual';

            $order = Order::create([
                'user_id' => $user->id,
                'item_id' => $shopManuscript->id,
                'type' => Order::MANUSCRIPT_TYPE,
                'package_id' => 0,
                'plan_id' => $paymentPlan?->id,
                'payment_mode_id' => $paymentMode?->id,
                'price' => $shopManuscript->price,
                'discount' => 0,
                'is_processed' => 0,
                'is_order_withdrawn' => 0,
            ]);

            $paymentUrl = null;
            $message = 'Order created. Payment is pending manual processing. Please contact support to complete payment.';

            if ($provider === 'vipps') {
                $vippsOrderId = $this->vippsOrderReference($order);
                $paymentUrl = app(\App\Http\Controllers\Controller::class)->vippsInitiatePayment([
                    'amount' => (int) round(((float) $shopManuscript->price) * 100),
                    'orderId' => $vippsOrderId,
                    'transactionText' => $shopManuscript->title,
                    'is_ajax' => true,
                    'vipps_phone_number' => optional($user->address)->vipps_phone_number,
                ]);

                if (! is_string($paymentUrl) || $paymentUrl === '') {
                    $provider = 'manual';
                    $paymentUrl = null;
                } else {
                    $order->svea_order_id = $vippsOrderId;
                    $message = 'Checkout created. Continue payment in Vipps.';
                    $order->save();
                }
            }

            OrderShopManuscript::create([
                'order_id' => $order->id,
                'genre' => $this->idempotencyGenre($idempotencyKey),
                'description' => json_encode([
                    'checkout' => [
                        'status' => 'pending',
                        'payment_provider' => $provider,
                        'payment_url' => $paymentUrl,
                        'message' => $message,
                    ],
                ]),
            ]);

            return $order->fresh(['shopManuscriptOrder', 'paymentMode']);
        });
    }

    public function cancelOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->refresh();

            if ((int) $order->is_processed === 1 || (int) ($order->is_order_withdrawn ?? 0) === 1) {
                return $order->load(['shopManuscriptOrder', 'paymentMode']);
            }

            $provider = strtolower((string) optional($order->paymentMode)->mode);

            if ($provider === 'vipps' && $order->svea_order_id) {
                app(\App\Repositories\VippsRepository::class)->cancelPayment((string) $order->svea_order_id);
            }

            $order->is_order_withdrawn = 1;
            $order->save();

            $metadata = json_decode((string) $order->shopManuscriptOrder?->description, true) ?: [];
            $metadata['checkout']['status'] = 'cancelled';
            $metadata['checkout']['message'] = 'Order cancelled.';

            if ($order->shopManuscriptOrder) {
                $order->shopManuscriptOrder->description = json_encode($metadata);
                $order->shopManuscriptOrder->save();
            }

            return $order->fresh(['shopManuscriptOrder', 'paymentMode']);
        });
    }

    public function markPaidByVippsReference(string $vippsOrderId, array $payload = []): ?Order
    {
        return DB::transaction(function () use ($vippsOrderId, $payload) {
            $order = Order::query()
                ->with(['shopManuscriptOrder', 'paymentMode'])
                ->where('type', Order::MANUSCRIPT_TYPE)
                ->where('svea_order_id', $vippsOrderId)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                return null;
            }

            if ((int) $order->is_processed === 0) {
                $order->is_processed = 1;
                $order->save();

                $exists = ShopManuscriptsTaken::query()
                    ->where('user_id', $order->user_id)
                    ->where('shop_manuscript_id', $order->item_id)
                    ->exists();

                if (! $exists) {
                    ShopManuscriptsTaken::create([
                        'user_id' => $order->user_id,
                        'shop_manuscript_id' => $order->item_id,
                        'is_active' => false,
                        'is_welcome_email_sent' => 0,
                    ]);
                }
            }

            $metadata = json_decode((string) $order->shopManuscriptOrder?->description, true) ?: [];
            $metadata['checkout']['status'] = 'paid';
            $metadata['checkout']['message'] = 'Payment captured and access granted.';
            $metadata['checkout']['provider_payload'] = $payload;

            if ($order->shopManuscriptOrder) {
                $order->shopManuscriptOrder->description = json_encode($metadata);
                $order->shopManuscriptOrder->save();
            }

            return $order->fresh(['shopManuscriptOrder', 'paymentMode']);
        });
    }

    private function assertCanPurchase(User $user, ShopManuscript $shopManuscript): void
    {
        if (! $user->could_buy_course) {
            throw new \DomainException('You are not allowed to buy shop manuscripts.');
        }

        $alreadyTaken = ShopManuscriptsTaken::query()
            ->where('user_id', $user->id)
            ->where('shop_manuscript_id', $shopManuscript->id)
            ->exists();

        if ($alreadyTaken) {
            throw new \DomainException('You already purchased this shop manuscript.');
        }

        $alreadyPaid = Order::query()
            ->where('user_id', $user->id)
            ->where('type', Order::MANUSCRIPT_TYPE)
            ->where('item_id', $shopManuscript->id)
            ->where('is_processed', 1)
            ->exists();

        if ($alreadyPaid) {
            throw new \DomainException('You already purchased this shop manuscript.');
        }
    }

    private function idempotencyGenre(string $idempotencyKey): string
    {
        return 'api-idempotency:'.hash('sha256', $idempotencyKey);
    }

    private function vippsOrderReference(Order $order): string
    {
        return sprintf('sm-%d-%d', $order->id, $order->user_id);
    }
}
