<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\ShopManuscriptCheckoutCancelRequest;
use App\Http\Requests\Api\V1\ShopManuscriptCheckoutStoreRequest;
use App\Http\Resources\Api\V1\ShopManuscriptCheckoutOrderResource;
use App\Order;
use App\ShopManuscript;
use App\Services\ShopManuscriptApiCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShopManuscriptCheckoutController extends ApiController
{
    public function store(ShopManuscriptCheckoutStoreRequest $request, int $id, ShopManuscriptApiCheckoutService $service): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $shopManuscript = ShopManuscript::query()->find($id);

        if (! $shopManuscript) {
            return $this->errorResponse('Shop manuscript not found.', 'not_found', 404);
        }

        try {
            Auth::setUser($user);
            $result = $service->createOrder($user, $shopManuscript, $request->validated()['idempotency_key'], $request);
            $order = $result['order'];
            $order->setAttribute('checkout_payment_url', $result['payment_url']);
            $order->setAttribute('checkout_message', $result['message']);
        } catch (\DomainException $exception) {
            return $this->errorResponse($exception->getMessage(), 'forbidden', 403);
        } catch (\Throwable $exception) {
            return $this->errorResponse('Unable to create checkout.', 'server_error', 500);
        }

        return (new ShopManuscriptCheckoutOrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, int $orderId, ShopManuscriptApiCheckoutService $service): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $order = Order::query()
            ->with(['shopManuscriptOrder', 'paymentMode'])
            ->find($orderId);

        if (! $order || Gate::forUser($user)->denies('viewShopManuscriptCheckoutOrder', $order)) {
            return $this->errorResponse('Checkout order not found.', 'not_found', 404);
        }

        $order = $service->syncOrderPaymentStatus($order);

        return response()->json((new ShopManuscriptCheckoutOrderResource($order))->resolve());
    }

    public function cancel(ShopManuscriptCheckoutCancelRequest $request, int $orderId, ShopManuscriptApiCheckoutService $service): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $order = Order::query()
            ->with(['shopManuscriptOrder', 'paymentMode'])
            ->find($orderId);

        if (! $order || Gate::forUser($user)->denies('cancelShopManuscriptCheckoutOrder', $order)) {
            return $this->errorResponse('Checkout order not found.', 'not_found', 404);
        }

        $order = $service->cancelOrder($order);

        return response()->json((new ShopManuscriptCheckoutOrderResource($order))->resolve());
    }

    public function vippsWebhook(Request $request, ShopManuscriptApiCheckoutService $service): JsonResponse
    {
        $signature = (string) $request->header('X-Shopmanuscript-Webhook-Token', '');
        $expected = (string) config('services.vipps.webhook_token', '');

        if ($expected !== '' && ! hash_equals($expected, $signature)) {
            return response()->json(['ok' => false], 401);
        }

        $orderReference = (string) data_get($request->all(), 'transactionInfo.orderId', '');
        $status = strtoupper((string) data_get($request->all(), 'transactionInfo.status', ''));

        if ($orderReference === '' || ! str_starts_with($orderReference, 'sm-')) {
            return response()->json(['ok' => true]);
        }

        if (in_array($status, ['CAPTURED', 'RESERVED', 'CAPTURE'], true)) {
            $service->markPaidByVippsReference($orderReference);
        }

        return response()->json(['ok' => true]);
    }
}
