<?php

namespace App\Repositories;

use App\Course;
use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\AdminHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Services\CourseService;
use App\Services\ShopManuscriptService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VippsRepository
{
    const PAYMENT_RESERVED = 'RESERVED';

    const PAYMENT_CANCELLED = 'CANCELLED';

    const PAYMENT_REJECTED = 'REJECTED';

    /**
     * Get the access token
     *
     * @return ApiException|array
     */
    public function getAccessToken()
    {
        $client_id = config('services.vipps.client_id');
        $client_secret = config('services.vipps.client_secret');

        $url = '/accesstoken/get';
        $method = 'POST';
        $header = [];
        $header[] = 'client_id: '.$client_id;
        $header[] = 'client_secret: '.$client_secret;
        $header[] = 'Content-Length: 0';

        $response = AdminHelpers::vippsAPI($method, $url, [], $header);        
        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            Log::info('VIPPS GET ACCESS TOKEN ERROR');
            Log::info(json_encode($response['data']));

            return new ApiException(property_exists($response['data'], 'message')
                ? $response['data']->message : $response['data']->error_description, null, $response['http_code']);
        }

        return $response;
    }

    /**
     * Initiate the payment process
     *
     * @return ApiException|array
     */
    public function initiatePayment($token_access, $data)
    {
        Log::info('VIPPS inside initiate payment');
        $url = '/ecomm/v2/payments';
        $method = 'POST';
        $header = [];
        $header[] = 'Authorization: '.$token_access;
        $fallbackUrl = isset($data['fallbackUrl']) ? $data['fallbackUrl'] : route('front.shop.thankyou'); // 'https://www.forfatterskolen.no/thankyou'

        $body = [
            'customerInfo' => [
                'mobileNumber' => isset($data['vipps_phone_number']) ? $data['vipps_phone_number'] : '',
            ],

            'merchantInfo' => [
                'callbackPrefix' => route('vipps.payment'), // url('/vipps/payment'),
                'fallBack' => route('vipps.fallback', ['t' => $data['orderId']]), // $fallbackUrl,//url('/thankyou'),
                'paymentType' => 'eComm Regular Payment',
                'merchantSerialNumber' => config('services.vipps.msn'), // AdminHelpers::generateHash(6)
            ],

            'transaction' => [
                'amount' => $data['amount'],
                'orderId' => $data['orderId'],
                'transactionText' => $data['transactionText'],
            ],
        ];

        $body = json_encode($body);
        $response = AdminHelpers::vippsAPI($method, $url, $body, $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            Log::info('VIPPS inside not success on initiate payment orderId = '.$data['orderId']);
            Log::info(json_encode($response));
            if (isset($response['data'][0])) {
                Log::info(json_encode($response['data'][0]));

                return new ApiException($response['data'][0]->errorMessage, null, $response['http_code']);
            }

            return new ApiException($response['data']->message, null, $response['http_code']);
        }

        return $response;
    }

    /**
     * @param  $request  Request
     */
    public function paymentCallback($orderId, $request)
    {
        $transactionInfo = $request['transactionInfo'];

        Log::info('payment callback here');
        Log::info(json_encode($transactionInfo));
        Log::info(json_encode($request->all()));
        // check if the payment is done
        if ($transactionInfo['status'] == self::PAYMENT_RESERVED) {
            $this->capturePayment($orderId);
        }

        if ($transactionInfo['status'] == self::PAYMENT_REJECTED) {
            Log::info('inside rejected here');
            if (strpos($orderId, '-') !== false) {
                Log::info('inside if');
                $expOrder = explode('-', $orderId);
                $order = Order::find($expOrder[0]);
                if ($order) {
                    Log::info('inside if order');
                    $route = $order->type === Order::MANUSCRIPT_TYPE ? 'front.shop-manuscript.checkout' : 'front.course.checkout';

                    return redirect()->route($route, $order->item_id);
                }
            }
        }
    }

    /**
     * Get the payment details of the order
     *
     * @return ApiException|array
     */
    public function getPaymentDetails($orderId, $token_access)
    {
        $url = '/ecomm/v2/payments/'.$orderId.'/details';
        $method = 'GET';
        $header = [];
        $header[] = 'Authorization: '.$token_access;

        $response = AdminHelpers::vippsAPI($method, $url, [], $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            if (isset($response['data'][0])) {
                return new ApiException($response['data'][0]->errorMessage, null, $response['http_code']);
            }

            return new ApiException($response['data']->message, null, $response['http_code']);
        }

        return $response;
    }

    /**
     * Capture payment by order od
     *
     * @return ApiException|array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function capturePayment($orderId)
    {
        $get_token = $this->getAccessToken();
        Log::info('VIPPS inside capture payment');
        if ($get_token instanceof ApiException) {
            return ApiResponse::error($get_token->getMessage(), $get_token->getData(), $get_token->getCode());
        }

        $access_token = $get_token['data']->access_token;

        $url = '/ecomm/v2/payments/'.$orderId.'/capture';
        $method = 'POST';
        $header = [];
        $header[] = 'Authorization: '.$access_token;

        $body = [
            'merchantInfo' => [
                'merchantSerialNumber' => config('services.vipps.msn'),
            ],

            'transaction' => [
                'transactionText' => 'Captured Payment for order #'.$orderId,
            ],
        ];

        $body = json_encode($body);
        $response = AdminHelpers::vippsAPI($method, $url, $body, $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            if (isset($response['data'][0])) {
                return new ApiException($response['data'][0]->errorMessage, null, $response['http_code']);
            }

            return new ApiException($response['data']->message, null, $response['http_code']);
        }
        Log::info('VIPPS inside capture payment after IF');
        $data = $response['data'];
        $invoice = Invoice::where('fiken_invoice_id', $orderId)->first(); // invoice_number
        $transactionInfo = $response['data']->transactionInfo;
        $message = '<p>Payment Captured <br/><br> Invoice Number: '.$orderId.' <br/> Amount:'.$transactionInfo->amount.' 
<br/> Transaction id: '.$transactionInfo->transactionId.'</p>';

        $subject = 'Payment Captured for Invoice #'.$orderId;
        $from = 'postmail@forfatterskolen.no';
        $to = 'support@forfatterskolen.no';
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = null;
        $emailData['attach_file'] = null;
        Log::info('VIPPS order id '.$orderId);
        Log::info('VIPPS inside capture payment before if captured');
        // notify admin once the payment is captured
        if ($transactionInfo->status == 'Captured') {
            Log::info('VIPPS inside capture payment inside captured');
            Log::info(json_encode($emailData));
            // mark the invoice as paid
            if ($invoice) {
                $invoice->fiken_is_paid = 1;
                $invoice->save();
            } else {
                $expOrderId = explode('-', $orderId);
                $order_id = $expOrderId[0];
                $user_id = $expOrderId[1];
                Log::info('VIPPS order id = '.$order_id.' and user id = '.$user_id);

                $order = Order::find($order_id);
                // add shop manuscript to user
                if (! $order->is_processed && $order->type === Order::MANUSCRIPT_TYPE) {
                    $shopManuscriptService = new ShopManuscriptService;
                    $shopManuscriptService->createInvoiceFromOder($order);
                    $shopManuscriptTaken = $shopManuscriptService->addShopManuscriptToLearner($order);
                    $shopManuscriptService->notifyAdmin($order);
                    $shopManuscriptService->notifyUser($order, $shopManuscriptTaken);
                }

                if (! $order->is_processed && $order->type === Order::COURSE_TYPE) {
                    $course = new Course;
                    $user = new User;
                    $courseService = new CourseService($course, $user);
                    $courseService->createInvoiceFromOder($order);
                    $courseTaken = $courseService->addCourseToLearner($order->user_id, $order->package_id);
                    $courseService->notifyAdmin($order->user_id, $order->package_id);
                    $courseService->notifyUser($order->user_id, $order->package_id, $courseTaken);
                }

                $order->is_processed = 1;
                $order->save();

            }

            // AdminHelpers::send_email($subject,$from, $to, $message);
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            \Mail::to('post@forfatterskolen.no')->queue(new SubjectBodyEmail($emailData));
            \Mail::to('elybutabara@gmail.com')->queue(new SubjectBodyEmail($emailData));
        }

        return $response;
    }
}
