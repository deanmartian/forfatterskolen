<?php

namespace App\Repositories;

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\AdminHelpers;
use App\Settings;
use Carbon\Carbon;

class VippsRepository extends BaseRepository {

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
    public function initiatePayment($token_access)
    {
        $url = '/ecomm/v2/payments';
        $method = "POST";
        $header = array();
        $header[] = 'Authorization: '.$token_access;

        $body = array(
            'customerInfo' => [
                'mobileNumber' => ''
            ],

            'merchantInfo' => [
                'callbackPrefix' => 'http://forfatterskolen.no/vipps/payment',//url('/vipps/payment'),
                'fallBack' => 'http://forfatterskolen.no/thankyou',//url('/thankyou'),
                'paymentType' => 'eComm Regular Payment',
                'merchantSerialNumber' => env('VIPPS_MSN_TEST')//AdminHelpers::generateHash(6)
            ],

            'transaction' => [
                'amount' => 100,
                'orderId' => 15,
                'transactionText' => 'Your order'
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

    public function paymentCallback($orderId, $request)
    {
        $new_settings['setting_name'] = 'paymentCallback';
        $new_settings['setting_value'] = $request;
        Settings::create($new_settings);
    }

}