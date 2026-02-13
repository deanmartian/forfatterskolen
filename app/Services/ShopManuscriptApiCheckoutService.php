<?php

namespace App\Services;

use App\Address;
use App\Http\FrontendHelpers;
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
use Illuminate\Support\Str;

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
                    return ['order' => $existing, 'payment_url' => null, 'gui_snippet' => null, 'message' => 'Checkout order created and awaiting payment.'];
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
            $price = $request->input('price');

            $paymentPlan = PaymentPlan::query()->find((int) $request->input('payment_plan_id'));
            $paymentMode = PaymentMode::query()->find((int) $request->input('payment_mode_id'));

            if (! $paymentPlan || ! $paymentMode) {
                throw new \DomainException('Invalid payment mode or payment plan.');
            }

            $order = Order::create([
                'user_id' => $user->id,
                'item_id' => $shopManuscript->id,
                'type' => Order::MANUSCRIPT_TYPE,
                'package_id' => 0,
                'plan_id' => $paymentPlan?->id,
                'payment_mode_id' => $paymentMode?->id,
                'price' => $price !== null ? (float) $price : $pricing['base_price'],
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
            $guiSnippet = null;
            $message = null;

            if ($this->isVippsMode($paymentMode)) {
                $vippsOrderId = $this->vippsOrderReference($order);
                $paymentUrl = app(\App\Http\Controllers\Controller::class)->vippsInitiatePayment([
                    'amount' => (int) round((($order->price + $order->additional) - $order->discount) * 100),
                    'orderId' => $vippsOrderId,
                    'transactionText' => $shopManuscript->title,
                    'fallbackUrl' => (string) $request->input('fallbackUrl', route('api.v1.vipps.fallback', ['t' => $vippsOrderId])),
                    'is_ajax' => true,
                    'vipps_phone_number' => optional($user->address)->vipps_phone_number,
                ]);

                if (is_string($paymentUrl) && $paymentUrl !== '') {
                    $order->svea_order_id = $vippsOrderId;
                    $order->save();
                    $message = 'Checkout created. Continue payment in Vipps.';
                } else {
                    throw new \DomainException('Unable to start Vipps checkout.');
                }
            }

            if ($this->isSveaMode($paymentMode)) {
                $svea = $this->initiateSveaCheckout($order, $shopManuscript, $user, $request);
                if ($svea['payment_url']) {
                    $order->svea_order_id = $svea['provider_order_id'];
                    $order->save();
                    $paymentUrl = $svea['payment_url'];
                    $guiSnippet = $svea['gui_snippet'];
                    $message = 'Checkout created. Continue payment in Svea.';
                } else {
                    throw new \DomainException('Unable to start Svea checkout.');
                }
            }

            if (! $this->isVippsMode($paymentMode) && ! $this->isSveaMode($paymentMode)) {
                throw new \DomainException('Unsupported payment mode for API checkout.');
            }

            return ['order' => $order->fresh(['shopManuscriptOrder', 'paymentMode']), 'payment_url' => $paymentUrl, 'gui_snippet' => $guiSnippet, 'message' => $message];
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

    public function syncOrderPaymentStatus(Order $order): Order
    {
        $order->loadMissing(['shopManuscriptOrder', 'paymentMode']);

        if ((int) $order->is_processed === 1) {
            return $order;
        }

        $mode = strtolower((string) optional($order->paymentMode)->mode);

        if ($mode !== 'svea' || ! $order->svea_order_id) {
            return $order;
        }

        $status = $this->resolveSveaStatus((string) $order->svea_order_id);

        if ($status === 'paid') {
            return $this->markOrderAsPaid($order);
        }

        if ($status === 'failed') {
            $order->setAttribute('checkout_message', 'Payment failed at provider.');
        }

        return $order;
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

            return $this->markOrderAsPaid($order);
        });
    }

    private function markOrderAsPaid(Order $order): Order
    {
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
    }

    private function resolveSveaStatus(string $sveaOrderId): string
    {
        $response = FrontendHelpers::sveaOrderDetails($sveaOrderId);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return 'pending';
        }

        $status = $response['Status'] ?? null;

        return match ($status) {
            'Final' => 'paid',
            'Cancelled', 'Invalid' => 'failed',
            default => 'pending',
        };
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

    private function isVippsMode(PaymentMode $paymentMode): bool
    {
        return strcasecmp((string) $paymentMode->mode, 'Vipps') === 0;
    }

    private function isSveaMode(PaymentMode $paymentMode): bool
    {
        if ((int) $paymentMode->id === 3) {
            return true;
        }

        return strcasecmp((string) $paymentMode->mode, 'Svea') === 0
            || strcasecmp((string) $paymentMode->mode, 'Faktura') === 0;
    }

    /**
     * @return array{provider_order_id:?string,payment_url:?string,gui_snippet:?string}
     */
    private function initiateSveaCheckout(Order $order, ShopManuscript $shopManuscript, User $user, Request $request): array
    {
        $merchantId = config('services.svea.checkoutid');
        $secret = config('services.svea.checkout_secret');

        if (! $merchantId || ! $secret) {
            return ['provider_order_id' => null, 'payment_url' => null, 'gui_snippet' => null];
        }

        $contact = $this->resolveContactData($user, $request);

        if (! $contact['email'] || ! $contact['zip'] || ! $contact['phone']) {
            return ['provider_order_id' => null, 'payment_url' => null, 'gui_snippet' => null];
        }

        $total = ($order->price + $order->additional) - $order->discount;
        $vatPercent = FrontendHelpers::userHasPaidCourse() ? 0 : 2500;

        try {
            $conn = \Svea\Checkout\Transport\Connector::init($merchantId, $secret, \Svea\Checkout\Transport\Connector::PROD_BASE_URL);
            $checkoutClient = new \Svea\Checkout\CheckoutClient($conn);

            $lovableBase = rtrim(config('api.lovable_url'), '/');

            $response = $checkoutClient->create([
                'countryCode' => config('services.svea.country_code'),
                'currency' => config('services.svea.currency'),
                'locale' => config('services.svea.locale'),
                'clientOrderNumber' => config('services.svea.identifier').$order->id,
                'merchantData' => $shopManuscript->title.' order',
                'cart' => [
                    'items' => [[
                        'name' => Str::limit($shopManuscript->title, 35),
                        'quantity' => 100,
                        'unitPrice' => (int) round($total * 100),
                        'unit' => 'pc',
                        'vatPercent' => $vatPercent,
                    ]],
                ],
                'presetValues' => [
                    ['typeName' => 'emailAddress', 'value' => $contact['email'], 'isReadonly' => false],
                    ['typeName' => 'postalCode', 'value' => $contact['zip'], 'isReadonly' => false],
                    ['typeName' => 'PhoneNumber', 'value' => $contact['phone'], 'isReadonly' => false],
                ],
                'merchantSettings' => [
                    'termsUri' => url('/terms/manuscript-terms'),
                    'checkoutUri' => url('/shop-manuscript/'.$shopManuscript->id.'/checkout?t=1'),
                    'confirmationUri' => $lovableBase.'/shop-manuscript/'.$shopManuscript->id.'/thankyou?svea_ord='.$order->id,
                    'pushUri' => url('/svea-callback?svea_order_id={checkout.order.uri}'),
                ],
            ]);

            return [
                'provider_order_id' => $response['OrderId'] ?? null,
                'payment_url' => $this->extractCheckoutUrl($response['Gui']['Snippet'] ?? ''),
                'gui_snippet' => $response['Gui']['Snippet'] ?? null,
            ];
        } catch (\Throwable $exception) {
            return ['provider_order_id' => null, 'payment_url' => null, 'gui_snippet' => null];
        }
    }

    /**
     * @return array{email:string,zip:string,phone:string}
     */
    private function resolveContactData(User $user, Request $request): array
    {
        $address = $user->address;

        $email = (string) ($request->input('email') ?: $user->email ?: '');
        $zip = (string) ($request->input('zip') ?: ($address->zip ?? ''));
        $phone = (string) ($request->input('phone') ?: ($address->phone ?? ''));

        if ($zip !== '' || $phone !== '') {
            Address::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'zip' => $zip ?: ($address->zip ?? null),
                    'phone' => $phone ?: ($address->phone ?? null),
                ]
            );
        }

        return [
            'email' => $email,
            'zip' => $zip,
            'phone' => $phone,
        ];
    }

    private function extractCheckoutUrl(string $guiSnippet): ?string
    {
        if ($guiSnippet === '') {
            return null;
        }

        if (preg_match('/data-sco-sveacheckout-iframesrc=["\\\']([^"\\\']+)["\\\']/i', $guiSnippet, $matches)) {
            return $matches[1] ?? null;
        }

        if (preg_match('/data-checkout-(?:url|uri)=["\\\']([^"\\\']+)["\\\']/', $guiSnippet, $matches)) {
            return $matches[1] ?? null;
        }

        if (preg_match('/src=["\\\']([^"\\\']+)["\\\']/', $guiSnippet, $matches)) {
            $candidate = $matches[1] ?? null;

            if ($candidate && str_contains($candidate, 'index.js')) {
                return null;
            }

            return $candidate;
        }

        return null;
    }
}
