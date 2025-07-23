<?php

namespace App\Services;

use App\CoachingTimerManuscript;
use App\Http\AdminHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoachingTimeService
{
    public function processPayLaterOrder(Request $request)
    {
        $orderRecord = $this->createOrder($request);
        $redirectUrl = url('/thank-you?pl_ord='.$orderRecord->id);

        return [
            'redirect_url' => $redirectUrl,
        ];
    }

    public function generateSveaCheckout(Request $request)
    {
        $orderRecord = $this->createOrder($request);

        $calculatedPrice = $request->price + $request->additional_price;
        $title = $request->item_id === 1 ? 'Coaching time (1 time)' : 'Coaching time (0,5 time)';

        $confirmationUrl = url('/thank-you?svea_ord='.$orderRecord->id);

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
                'merchantData' => $title.' order',
                'cart' => [
                    'items' => [
                        [
                            'name' => \Illuminate\Support\Str::limit($title, 35),
                            'quantity' => 100,
                            'unitPrice' => $calculatedPrice * 100,
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
                    'termsUri' => url('/terms/coaching-terms'),
                    'checkoutUri' => url('/coaching-timer/checkout/'.$request->item_id), // load checkout
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

    public function createOrder(Request $request)
    {

        $newOrder['user_id'] = \Auth::user()->id;
        $newOrder['item_id'] = $request->item_id;
        $newOrder['type'] = Order::COACHING_TIME_TYPE;
        $newOrder['package_id'] = 0;
        $newOrder['plan_id'] = $request->payment_plan_id;
        $newOrder['price'] = $request->price;
        $newOrder['discount'] = 0;
        $newOrder['payment_mode_id'] = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;
        $newOrder['is_pay_later'] = filter_var($request->is_pay_later, FILTER_VALIDATE_BOOLEAN);

        if ($request->has('additional')) {
            $newOrder['additional'] = $request->additional;
        }

        $order = Order::create($newOrder);

        $suggested_dates = explode(',', $request->suggested_date);

        // format the sent suggested dates
        /* foreach ($suggested_dates as $k => $suggested_date) {
            $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
        } */

        $order->coachingTime()->create([
            'additional_price' => $request->additional_price,
            'file' => $request->fileLocation,
            'suggested_date' => null, // json_encode($suggested_dates),
            'help_with' => $request->help_with,
        ]);

        return $order;
    }

    public function addCoachingTime($order)
    {

        $coachingTime = $order->coachingTime;
        $newFileLocation = null;

        if ($coachingTime->file) {
            // move the file to another location
            $file = explode('/', $coachingTime->file);
            $fileName = $file[2];
            $destination = 'storage/coaching-timer-manuscripts/';
            $time = time();
            $getExtension = explode('.', $fileName);
            $extension = $getExtension[1];

            $newFileLocation = $destination.$time.'.'.$extension;
            // move the file from manuscript-tests to shop-manuscripts
            \File::copy($coachingTime->file, $destination.$time.'.'.$extension);
        }

        return CoachingTimerManuscript::create([
            'user_id' => $order->user_id,
            'file' => $newFileLocation,
            'payment_price' => $order->price + $coachingTime->additional_price,
            'plan_type' => $order->item_id,
            'suggested_date' => $coachingTime->suggested_date,
            'help_with' => $coachingTime->help_with,
        ]);
    }

    public function notifyUser($order, $coaching)
    {
        // Send Email
        $user = $order->user;

        $emailTemplate = AdminHelpers::emailTemplate('Coaching Order');
        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user->email,
            $user->first_name, '');

        dispatch(new AddMailToQueueJob($user->email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, 'Forfatterskolen', null,
            'coaching-time-order', $coaching->id));
    }

    public function notifyAdmin($order)
    {
        $user = $order->user;

        $title = 'Coaching time';

        if ($order->item_id == 1) {
            $title .= ' (1 time)';
        } else {
            $title .= ' (0,5 time)';
        }

        $message = $user->full_name.' has ordered the '.$title;
        $to = 'post@forfatterskolen.no';
        $emailData = [
            'email_subject' => 'New Coaching Session',
            'email_message' => $message,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
    }
}
