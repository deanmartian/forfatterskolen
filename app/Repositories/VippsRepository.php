<?php

namespace App\Repositories;

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\AdminHelpers;
use App\Invoice;
use App\Mail\SubjectBodyEmail;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VippsRepository extends BaseRepository {

    const PAYMENT_RESERVED = 'RESERVED';
    const PAYMENT_CANCELLED = 'CANCELLED';
    /**
     * Get the access token
     * @return ApiException|array
     */
    public function getAccessToken()
    {
        $client_id = env('VIPPS_CLIENT_ID_TEST');
        $client_secret = env('VIPPS_CLIENT_SECRET_TEST');

        $url = '/accesstoken/get';
        $method = "POST";
        $header = array();
        $header[] = 'client_id: '.$client_id;
        $header[] = 'client_secret: '.$client_secret;
        $header[] = 'Content-Length: 0';

        $response = AdminHelpers::vippsAPI($method, $url, [], $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            return new ApiException($response['data']->message, null, $response['http_code']);
        }

        return $response;
    }

    /**
     * Initiate the payment process
     * @param $token_access
     * @return ApiException|array
     */
    public function initiatePayment($token_access, $data)
    {
        Log::info("VIPPS inside initiate payment");
        $url = '/ecomm/v2/payments';
        $method = "POST";
        $header = array();
        $header[] = 'Authorization: '.$token_access;
        $fallbackUrl = isset($data['fallbackUrl']) ? $data['fallbackUrl'] : route('front.shop.thankyou');//'https://www.forfatterskolen.no/thankyou'

        $body = array(
            'customerInfo' => [
                'mobileNumber' => ''
            ],

            'merchantInfo' => [
                'callbackPrefix' => 'https://www.forfatterskolen.no/vipps/payment',//url('/vipps/payment'),
                'fallBack' => $fallbackUrl,//url('/thankyou'),
                'paymentType' => 'eComm Regular Payment',
                'merchantSerialNumber' => env('VIPPS_MSN_TEST')//AdminHelpers::generateHash(6)
            ],

            'transaction' => [
                'amount' => $data['amount'],
                'orderId' => $data['orderId'],
                'transactionText' => $data['transactionText']
            ]
        );

        $body = json_encode($body);
        $response = AdminHelpers::vippsAPI($method, $url, $body, $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            if (isset($response['data'][0])) {
                return new ApiException($response['data'][0]->errorMessage, null, $response['http_code']);
            }

            return new ApiException($response['data']->message, null, $response['http_code']);
        }

        return $response;
    }

    /**
     * @param $orderId
     * @param $request Request
     */
    public function paymentCallback($orderId, $request)
    {
        $transactionInfo = $request['transactionInfo'];

        // check if the payment is done
        if ($transactionInfo['status'] == self::PAYMENT_RESERVED) {
            $this->capturePayment($orderId);
        }
    }

    /**
     * Get the payment details of the order
     * @param $orderId
     * @param $token_access
     * @return ApiException|array
     */
    public function getPaymentDetails($orderId, $token_access)
    {
        $url = '/ecomm/v2/payments/'.$orderId.'/details';
        $method = "GET";
        $header = array();
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
     * @param $orderId
     * @return ApiException|array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function capturePayment($orderId)
    {
        $get_token = $this->getAccessToken();
        Log::info("VIPPS inside capture payment");
        if ($get_token instanceof ApiException) {
            return ApiResponse::error($get_token->getMessage(), $get_token->getData(), $get_token->getCode());
        }

        $access_token = $get_token['data']->access_token;

        $url = '/ecomm/v2/payments/'.$orderId.'/capture';
        $method = "POST";
        $header = array();
        $header[] = 'Authorization: '.$access_token;

        $body = array(
            'merchantInfo' => [
                'merchantSerialNumber' => env('VIPPS_MSN_TEST')
            ],

            'transaction' => [
                'transactionText' => 'Captured Payment for order #'.$orderId
            ]
        );

        $body = json_encode($body);
        $response = AdminHelpers::vippsAPI($method, $url, $body, $header);

        if ($response['http_code'] != ApiResponse::HTTPCODE_SUCCESS) {
            if (isset($response['data'][0])) {
                return new ApiException($response['data'][0]->errorMessage, null, $response['http_code']);
            }

            return new ApiException($response['data']->message, null, $response['http_code']);
        }
        Log::info("VIPPS inside capture payment after IF");
        $data = $response['data'];
        $invoice = Invoice::where('invoice_number',$orderId)->first();
        $transactionInfo = $response['data']->transactionInfo;
        $message = "<p>Payment Captured <br/><br> Invoice Number: ".$invoice->invoice_number." <br/> Amount:".$transactionInfo->amount." 
<br/> Transaction id: ".$transactionInfo->transactionId."</p>";

        $subject = 'Payment Captured for Invoice #'.$invoice->invoice_number;
        $from = 'postmail@forfatterskolen.no';
        $to = 'support@forfatterskolen.no';
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = NULL;
        $emailData['from_email'] = NULL;
        $emailData['attach_file'] = NULL;
        Log::info("VIPPS inside capture payment before if captured");
        // notify admin once the payment is captured
        if ($transactionInfo->status == 'Captured') {
            Log::info("VIPPS inside capture payment inside captured");
            Log::info(json_encode($emailData));
            // mark the invoice as paid
            $invoice->fiken_is_paid = 1;
            $invoice->save();

            //AdminHelpers::send_email($subject,$from, $to, $message);
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
            \Mail::to('post@forfatterskolen.no')->queue(new SubjectBodyEmail($emailData));
            \Mail::to('elybutabara@gmail.com')->queue(new SubjectBodyEmail($emailData));
        }

        return $response;
    }

}