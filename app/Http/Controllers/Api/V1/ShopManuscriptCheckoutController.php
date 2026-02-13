<?php

namespace App\Http\Controllers\Api\V1;

use App\CheckoutLog;
use App\Http\Requests\Api\V1\ShopManuscriptCheckoutCancelRequest;
use App\Http\Requests\Api\V1\ShopManuscriptCheckoutStoreRequest;
use App\Http\Resources\Api\V1\ShopManuscriptCheckoutOrderResource;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Helpers\ApiException;
use App\Order;
use App\ShopManuscript;
use App\Services\ShopManuscriptService;
use Carbon\Carbon;
use App\Services\ShopManuscriptApiCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\VippsRepository;

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
        } catch (\DomainException $exception) {
            return $this->errorResponse($exception->getMessage(), 'validation_error', 422);
        } catch (\Throwable $exception) {
            return $this->errorResponse('Unable to create checkout.', 'server_error', 500);
        }

        return response()->json([
            'redirect_url' => $result['payment_url'],
            'gui_snippet' => $result['gui_snippet'] ?? null,
            'reference' => $order->id,
        ], 201);
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


    public function vippsFallback(Request $request, ShopManuscriptApiCheckoutService $service, VippsRepository $vippsRepository): JsonResponse
    {
        $orderReference = (string) $request->query('t', '');

        if ($orderReference === '') {
            return $this->errorResponse('Missing Vipps order reference.', 'validation_error', 422);
        }

        $order = Order::query()
            ->where('type', Order::MANUSCRIPT_TYPE)
            ->where('svea_order_id', $orderReference)
            ->first();

        if (! $order) {
            return $this->errorResponse('Checkout order not found.', 'not_found', 404);
        }

        $tokenResponse = $vippsRepository->getAccessToken();

        if ($tokenResponse instanceof ApiException) {
            return $this->errorResponse('Unable to read Vipps payment status.', 'provider_error', 502);
        }

        $detailsResponse = $vippsRepository->getPaymentDetails($orderReference, $tokenResponse['data']->access_token);

        if ($detailsResponse instanceof ApiException) {
            return $this->errorResponse('Unable to read Vipps payment status.', 'provider_error', 502);
        }

        $transactionHistory = data_get($detailsResponse, 'data.transactionLogHistory.0');
        $isCaptured = strtoupper((string) data_get($transactionHistory, 'operation', '')) === 'CAPTURE'
            && (bool) data_get($transactionHistory, 'operationSuccess', false);

        if ($isCaptured) {
            $order = $service->markPaidByVippsReference($orderReference) ?? $order;
        }

        return response()->json([
            'status' => $isCaptured ? 'paid' : 'pending',
            'order_id' => $order->id,
            'item_id' => $order->item_id,
            'reference' => $orderReference,
            'is_processed' => (bool) $order->is_processed,
        ]);
    }

    public function thankyou(Request $request, int $id, ShopManuscriptService $shopManuscriptService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if (! $request->has('svea_ord') && ! $request->has('pl_ord')) {
            return $this->errorResponse('Missing order reference.', 'validation_error', 422);
        }

        $orderId = (int) ($request->input('svea_ord') ?? $request->input('pl_ord'));

        $order = Order::query()
            ->where('id', $orderId)
            ->where('item_id', $id)
            ->where('type', Order::MANUSCRIPT_TYPE)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return $this->errorResponse('Order not found.', 'not_found', 404);
        }

        if ($request->has('svea_ord')) {
            SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
        }

        if (! $order->is_processed) {
            $shopManuscriptTaken = $shopManuscriptService->addShopManuscriptToLearner($order);
            $shopManuscriptService->notifyAdmin($order);
            $shopManuscriptService->notifyUser($order, $shopManuscriptTaken);
        }

        $order->is_processed = 1;
        $order->save();

        CheckoutLog::updateOrCreate([
            'user_id' => $order->user_id,
            'parent' => 'shop-manuscript',
            'parent_id' => $id,
        ], [
            'is_ordered' => true,
        ]);

        return response()->json([
            'status' => 'ok',
            'order_id' => $order->id,
            'is_processed' => (bool) $order->is_processed,
        ]);
    }
}
