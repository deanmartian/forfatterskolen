<?php

namespace App\Http\Controllers\Frontend;

use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Order;
use App\PublishingService;
use App\SelfPublishing;
use App\SelfPublishingLearner;
use App\SelfPublishingOrder;
use App\Services\CourseService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublishingServiceController extends Controller
{
    public function show($id): View
    {
        $service = PublishingService::findOrFail($id);
        abort(404);

        return view('frontend.publising-service.checkout', compact('service'));
    }

    public function validateForm(Request $request, CourseService $courseService): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        if (! \Auth::check()) {
            $validation['password'] = 'required|min:3';
        }

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! \Auth::check()) {
            $addressData = [
                'street' => $request->street,
                'zip' => $request->zip,
                'city' => $request->city,
                'phone' => $request->phone,
            ];
            $courseService->evaluateUser(
                $request->email,
                $request->password,
                $request->first_name,
                $request->last_name,
                $addressData
            );
        }

        $user = \Auth::user();

        $this->addToCart($request);
        $this->processCheckoutOrder();
    }

    public function thankyou(): View
    {
        return view('frontend.publising-service.thankyou');
    }

    public function addToCart(Request $request)
    {
        $file = null;

        if ($request->has('file')) {
            $file = FrontendHelpers::saveFile($request, 'self_publishing_order', 'file');
        }

        $title = $request->title === 'null' ? null : $request->title;
        $description = $request->description === 'null' ? null : $request->description;

        SelfPublishingOrder::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'parent' => $request->parent,
            'parent_id' => $request->parent_id,
            'title' => $title,
            'description' => $description,
            'file' => $file,
            'price' => floatval($request->totalPrice),
            'word_count' => $request->word_count,
            'status' => 'active',
        ]);

    }

    public function processCheckoutOrder()
    {
        $currentOrderQuery = SelfPublishingOrder::active()->where('user_id', Auth::id());
        $currentOrders = $currentOrderQuery->get();
        $currentOrderTotal = $currentOrderQuery->sum('price');

        $order = Order::create([
            'user_id' => Auth::id(),
            'item_id' => $currentOrders[0]->id,
            'type' => Order::EDITING_SERVICES,
            'plan_id' => 8,
            'price' => $currentOrderTotal,
            'discount' => 0,
            'is_processed' => 1,
        ]);

        SelfPublishingOrder::whereIn('id', $currentOrders->pluck('id'))
            ->update([
                'order_id' => $order->id,
                'status' => 'paid',
            ]);

        foreach ($currentOrders as $currentOrder) {
            $publishingService = PublishingService::find($currentOrder->parent_id);

            if ($publishingService->slug === 'sprakvask') {
                CopyEditingManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0,
                ]);
            }

            if ($publishingService->slug === 'korrektur') {
                CorrectionManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0,
                ]);
            }

            // redaktor
            if ($publishingService->id === 3) {
                $selfPublishing = SelfPublishing::create([
                    'title' => $currentOrder->title,
                    'description' => $currentOrder->description,
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'manuscript' => $currentOrder->file,
                    'word_count' => $currentOrder->word_count,
                    'price' => $currentOrder->price,
                ]);

                SelfPublishingLearner::create([
                    'self_publishing_id' => $selfPublishing->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    public function serviceCalculator(): View
    {
        $serviceList = PublishingService::where('is_active', 1)->orderBy('service_type', 'ASC')->get();

        return view('frontend.publising-service.service-calculator', compact('serviceList'));
    }
}
