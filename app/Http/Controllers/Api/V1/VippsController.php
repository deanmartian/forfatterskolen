<?php

namespace App\Http\Controllers\Api\V1;

use App\Order;
use App\Repositories\VippsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VippsController extends ApiController
{
    public function fallback(Request $request, VippsRepository $vippsRepository)/* : JsonResponse */
    {
        Log::info("------------- inside vipps fallback -------------");
        $orderReference = (string) $request->query('t', '');
        Log::info(" order reference $orderReference");

        if ($orderReference === '') {
            return $this->errorResponse('Missing Vipps order reference.', 'validation_error', 422);
        }

        $expOrder = explode('-', $orderReference);
        $order = isset($expOrder[1]) ? Order::find((int) $expOrder[1]) : null; // use 1 instead of 0 since ny.fs include sm-

        Log::info(json_encode($expOrder));
        Log::info(json_encode($order));

        $tokenResponse = $vippsRepository->getAccessToken();
        Log::info(json_encode($tokenResponse));
        if ($tokenResponse instanceof \App\Helpers\ApiException) {
            return $this->errorResponse('Unable to read Vipps payment status.', 'provider_error', 502);
        }

        $vippsOrder = $vippsRepository->getPaymentDetails($orderReference, $tokenResponse['data']->access_token);

        if ($vippsOrder instanceof \App\Helpers\ApiException) {
            return $this->errorResponse('Unable to read Vipps payment status.', 'provider_error', 502);
        }

        $transactionHistory = data_get($vippsOrder, 'data.transactionLogHistory.0');

        if ($transactionHistory && $order) {
            $route = $order->type === Order::MANUSCRIPT_TYPE ? 'front.shop-manuscript.cancelled-order' : 'front.course.cancelled-order';
            $isCaptured = strtoupper((string) data_get($transactionHistory, 'operation', '')) === 'CAPTURE'
                && (bool) data_get($transactionHistory, 'operationSuccess', false);

            if ($isCaptured) {
                $route = $order->type === Order::MANUSCRIPT_TYPE ? 'front.shop-manuscript.thankyou' : 'front.shop.thankyou';
            }

            /* return response()->json([
                'status' => $isCaptured ? 'paid' : 'cancelled',
                'order_id' => $order->id,
                'item_id' => $order->item_id,
                'reference' => $orderReference,
                'route' => $route,
                'route_params' => ['id' => $order->item_id],
            ]); */
        }

        return redirect()->to(rtrim(config('api.lovable_url') .'/vipps/fallback?t=' . $orderReference, '/'));
        /* return response()->json([
            'status' => 'unknown',
            'reference' => $orderReference,
            'route' => 'front.thank-you',
            'route_params' => [],
        ]); */
    }
}
