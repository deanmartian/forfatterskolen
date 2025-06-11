<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Repositories\VippsRepository;

class VippsController extends Controller
{
    protected $access_token = '';

    protected $repository;

    /**
     * VippsController constructor.
     */
    public function __construct(VippsRepository $repository)
    {
        $this->repository = $repository;
        $result = $repository->getAccessToken();

        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }

        $this->access_token = $result['data']->access_token;
    }

    /**
     * Initiate the payment
     *
     * @param  VippsRepository  $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $data = ['amount' => 100, 'orderId' => 27, 'transactionText' => 'this is a test order'];
        $result = $this->repository->initiatePayment($this->access_token, $data);
        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }

        return redirect()->to($result['data']->url);
    }

    public function getPaymentDetails($orderId)
    {
        $result = $this->repository->getPaymentDetails($orderId, $this->access_token);
        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }

        print_r($result);

        // check for transactionSummary
        if (property_exists($result['data'], 'transactionSummary')) {
            echo 'has transaction';
        }
    }
}
