<?php

namespace App\Services;

use App\Order;
use App\OrderShopManuscript;
use App\PaymentMode;
use App\PaymentPlan;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShopManuscriptApiCheckoutService
{
    public function __construct(private readonly ShopManuscriptService $shopManuscriptService)
    {
    }

    public function createOrder(User $user, ShopManuscript $shopManuscript, string $idempotencyKey, Request $request): array
    {
        return DB::transaction(function () use ($user, $shopManuscript, $idempotencyKey, $request) {
            $cacheKey = $this->idempotencyCacheKey($user->id, $shopManuscript->id, $idempotencyKey);
            $cachedOrderId = Cache::get($cacheKey);

            if ($cachedOrderId) {
                $existing = Order::query()->with(['shopManuscriptOrder', 'paymentMode'])->find((int) $cachedOrderId);
                if ($existing && (int) $existing->user_id === (int) $user->id && (int) $existing->item_id === (int) $shopManuscript->id) {
                    return ['order' => $existing, 'payment_url' => null, 'message' => 'Checkout order created and awaiting payment.'];
                }
            }

            $this->assertCanPurchase($user, $shopManuscript);

            $uploaded = $this->shopManuscriptService->uploadLearnerManuscript($request, (int) $user->id);
            $wordCount = (int) ($uploaded['word_count'] ?? 0);
            $filePath = $uploaded['manuscript_file'] ?? null;

            if ($wordCount <= 0 || ! $filePath) {
                throw new \DomainException(trans('site.invalid-manuscript-word-count'));
            }

            if ($wordCount > (int) $shopManuscript->max_words) {
                throw new \DomainException('Uploaded manuscript exceeds the selected plan word limit.');
            }

            $pricing = $this->calculatePricing($shopManuscript, $wordCount);

            $paymentPlan = PaymentPlan::query()->where('division', 1)->orderBy('id')->first();
            $paymentMode = PaymentMode::query()->whereIn('mode', ['Vipps', 'Svea'])->orderByRaw("FIELD(mode, 'Vipps', 'Svea')")->first();

            $order = Order::create([
                'user_id' => $user->id,
                'item_id' => $shopManuscript->id,
                'type' => Order::MANUSCRIPT_TYPE,
                'package_id' => 0,
                'plan_id' => $paymentPlan?->id,
                'payment_mode_id' => $paymentMode?->id,
                'price' => $pricing['base_price'],
                'discount' => 0,
                'additional' => $pricing['excess_amount'],
                'is_processed' => 0,
                'is_order_withdrawn' => 0,
            ]);

            $synopsis = $this->shopManuscriptService->uploadSynopsis($request);

            OrderShopManuscript::create([
                'order_id' => $order->id,
                'genre' => (string) $request->input('genre'),
                'file' => '/'.ltrim((string) $filePath, '/'),
                'words' => $wordCount,
                'description' => $request->input('description'),
                'synopsis' => $synopsis,
                'coaching_time_later' => $request->boolean('coaching_time_later'),
                'send_to_email' => $request->boolean('send_to_email'),
            ]);

            Cache::put($cacheKey, $order->id, now()->addHours(24));

            $paymentUrl = null;
            $message = 'Order created. Payment is pending manual processing. Please contact support to complete payment.';

            if ($paymentMode && $paymentMode->mode === 'Vipps') {
                $vippsOrderId = $this->vippsOrderReference($order);
                $paymentUrl = app(\App\Http\Controllers\Controller::class)->vippsInitiatePayment([
                    'amount' => (int) round((($order->price + $order->additional) - $order->discount) * 100),
                    'orderId' => $vippsOrderId,
                    'transactionText' => $shopManuscript->title,
                    'is_ajax' => true,
                    'vipps_phone_number' => optional($user->address)->vipps_phone_number,
                ]);

                if (is_string($paymentUrl) && $paymentUrl !== '') {
                    $order->svea_order_id = $vippsOrderId;
                    $order->save();
                    $message = 'Checkout created. Continue payment in Vipps.';
                } else {
                    $paymentUrl = null;
                }
            }

            return ['order' => $order->fresh(['shopManuscriptOrder', 'paymentMode']), 'payment_url' => $paymentUrl, 'message' => $message];
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

            return $order->fresh(['shopManuscriptOrder', 'paymentMode']);
        });
    }

    public function markPaidByVippsReference(string $vippsOrderId): ?Order
    {
        return DB::transaction(function () use ($vippsOrderId) {
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
                    $taken = new ShopManuscriptsTaken;
                    $taken->user_id = $order->user_id;
                    $taken->shop_manuscript_id = $order->item_id;
                    $taken->genre = $order->shopManuscriptOrder?->genre;
                    $taken->description = $order->shopManuscriptOrder?->description;
                    $taken->file = $order->shopManuscriptOrder?->file;
                    $taken->words = $order->shopManuscriptOrder?->words;
                    $taken->synopsis = $order->shopManuscriptOrder?->synopsis;
                    $taken->is_active = false;
                    $taken->coaching_time_later = $order->shopManuscriptOrder?->coaching_time_later;
                    $taken->is_welcome_email_sent = 0;
                    $taken->save();
                }
            }

            return $order->fresh(['shopManuscriptOrder', 'paymentMode']);
        });
    }

    private function assertCanPurchase(User $user, ShopManuscript $shopManuscript): void
    {
        if (! $user->could_buy_course) {
            throw new \DomainException('You are not allowed to buy shop manuscripts.');
        }
    }

    private function calculatePricing(ShopManuscript $shopManuscript, int $wordCount): array
    {
        $excessPerWordAmount = \App\Http\FrontendHelpers::manuscriptExcessPerWordPrice();
        $basePrice = (float) $shopManuscript->full_payment_price;
        $excessWords = $wordCount - 17500;

        if (in_array((int) $shopManuscript->id, [3, 9], true)) {
            $excessWords = $wordCount - 5000;
            $excessPerWordAmount = 0.112;
            $basePrice = 1500;
        }

        return [
            'base_price' => $basePrice,
            'excess_amount' => $excessWords > 0 ? $excessWords * $excessPerWordAmount : 0,
        ];
    }

    private function idempotencyCacheKey(int $userId, int $shopManuscriptId, string $idempotencyKey): string
    {
        return sprintf('api:shop-manuscript-checkout:%d:%d:%s', $userId, $shopManuscriptId, hash('sha256', $idempotencyKey));
    }

    private function vippsOrderReference(Order $order): string
    {
        return sprintf('sm-%d-%d', $order->id, $order->user_id);
    }
}
