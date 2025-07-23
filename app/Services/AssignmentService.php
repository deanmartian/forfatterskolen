<?php

namespace App\Services;

use App\Assignment;
use App\AssignmentAddon;
use App\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentService
{
    public function generateSveaCheckout(Request $request): JsonResponse
    {
        $assignment = Assignment::find($request->assignment_id);
        $price = (int) $request->price;

        $orderRecord = $this->createOrder($request);
        $confirmationUrl = url('/assignment/thankyou?svea_ord='.$orderRecord->id);

        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_BASE_URL;

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Create Order
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException - if $orderId is missing
             * \Svea\Checkout\Exception\SveaApiException - is there is some problem with api connection or
             *      some error occurred with data validation on API side
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutClient($conn);

            /**
             * create order
             */
            $data = [
                'countryCode' => config('services.svea.country_code'),
                'currency' => config('services.svea.currency'),
                'locale' => config('services.svea.locale'),
                'clientOrderNumber' => config('services.svea.identifier').$orderRecord->id, // rand(10000,30000000),
                'merchantData' => $assignment->title.' order',
                'cart' => [
                    'items' => [
                        [
                            'name' => \Illuminate\Support\Str::limit($assignment->title, 35),
                            'quantity' => 100,
                            'unitPrice' => $price * 100,
                            'unit' => 'pc',
                        ],
                    ],
                ],
                'presetValues' => [
                    [
                        'typeName' => 'emailAddress',
                        'value' => $request->email,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'postalCode',
                        'value' => $request->zip,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'PhoneNumber',
                        'value' => $request->phone,
                        'isReadonly' => false,
                    ],
                ],
                'merchantSettings' => [
                    'termsUri' => url('/terms/manuscript-terms'),
                    'checkoutUri' => url('/account/upgrade/assignment/'.$request->assignment_id), // load checkout
                    'confirmationUri' => $confirmationUrl,
                    'pushUri' => url('/svea-callback?svea_order_id={checkout.order.uri}'),
                    // "https://localhost:51925/push.php?svea_order_id={checkout.order.uri}",
                ],
            ];

            $response = $checkoutClient->create($data);
            $orderId = $response['OrderId'];
            $guiSnippet = $response['Gui']['Snippet'];
            $orderStatus = $response['Status'];
            $orderRecord->svea_order_id = $orderId;
            $orderRecord->save(); // update the checkout and save the order id from svea

            return $guiSnippet;

        } catch (\Svea\Checkout\Exception\SveaApiException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaConnectorException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaInputValidationException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder(Request $request)
    {
        $newOrder['user_id'] = \Auth::user()->id;
        $newOrder['item_id'] = $request->assignment_id;
        $newOrder['type'] = $request->order_type;
        $newOrder['package_id'] = 0;
        $newOrder['plan_id'] = $request->payment_plan_id;
        $newOrder['price'] = $request->price;
        $newOrder['discount'] = 0;
        $newOrder['payment_mode_id'] = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;

        return Order::create($newOrder);
    }

    public function upgradeAssignment($order)
    {
        $assignment = Assignment::find($order->item_id);
        if ($assignment) {

            AssignmentAddon::create([
                'user_id' => $order->user_id,
                'assignment_id' => $assignment->id,
            ]);
        }
    }
}
