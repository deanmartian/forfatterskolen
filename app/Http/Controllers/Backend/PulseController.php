<?php
namespace App\Http\Controllers\Backend;

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Helpers\DapulseRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PulseController extends Controller
{
    public function index(DapulseRepository $repository)
    {
        $result = $repository->getBoards();

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        \Session::put('pulses', $result);

        return view('backend.pulse.index', compact('result'));
    }

    /**
     * Update title of specific pulse
     * @param $pulse_id
     * @param Request $request
     * @param DapulseRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePulseTitle($pulse_id, Request $request, DapulseRepository $repository)
    {
        $result = $repository->updatePulseTitle($pulse_id, $request);

        if ($result instanceof ApiException) {
            return response()->json(ApiResponse::error($result->getMessage()), $result->getCode());
        }
        return response()->json('', 200);
    }

    public function removeSubscriber(Request $request, DapulseRepository $repository)
    {
        $result = $repository->removePulseSubscriber($request);
print_r($result);
        /*if ($result instanceof ApiException) {
            return response()->json(ApiResponse::error($result->getMessage()), $result->getCode());
        }

        return redirect()->back();*/
    }
}