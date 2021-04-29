<?php

namespace App\Http\Controllers;

use App\EmailTemplate;
use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Log;
use App\Repositories\VippsRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $data array
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function vippsInitiatePayment($data)
    {
        $repository = new VippsRepository();
        $result = $repository->getAccessToken(); // get the access token

        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }

        $access_token = $result['data']->access_token;

        $initiatePaymentResult = $repository->initiatePayment($access_token, $data); // initiate the payment

        if ($initiatePaymentResult instanceof ApiException) {
            return ApiResponse::error($initiatePaymentResult->getMessage(), $initiatePaymentResult->getData(),
                $initiatePaymentResult->getCode());
        }

        if (isset($data['is_ajax'])) {
            \Illuminate\Support\Facades\Log::info("VIPPS inside is ajax");
            return $initiatePaymentResult['data']->url;
        }

        return redirect()->to($initiatePaymentResult['data']->url);
    }

    public function emailTemplate($page_name)
    {
        return EmailTemplate::where('page_name', $page_name)->first();
    }
}
