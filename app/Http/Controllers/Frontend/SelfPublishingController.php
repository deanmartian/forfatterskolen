<?php

namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Controller;
use App\Order;
use App\PublishingService;
use App\SelfPublishing;
use App\SelfPublishingOrder;
use Auth;
use FrontendHelpers;
use Illuminate\Http\Request;

class SelfPublishingController extends Controller
{
    
    public function selfPublishingOrder() {
        $currentOrderQuery = SelfPublishingOrder::active()->where('user_id', Auth::id());
        $currentOrders = $currentOrderQuery->get();
        $currentOrderTotal = $currentOrderQuery->sum('price');

        $orderHistoryQuery = SelfPublishingOrder::paid()->where('user_id', Auth::id());
        $orderHistory = $orderHistoryQuery->get();
        $orderHistoryTotal = $orderHistoryQuery->sum('price');

        $savedQuotes = SelfPublishingOrder::quote()->where('user_id', Auth::id())->get();

        return view('frontend.learner.self-publishing.order.index', compact('currentOrders', 'currentOrderTotal', 'orderHistory',
            'orderHistoryTotal', 'savedQuotes'));
    }

    public function addToCart(Request $request)
    {
        $file = NULL;
        
        if ($request->has('file')) {
            $file = FrontendHelpers::saveFile($request, 'self_publishing_order', 'file');
        }

        $title = $request->title === "null" ? NULL : $request->title;
        $description = $request->description === "null" ? NULL : $request->description;

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
            'status' => 'active'
        ]);
        return $request->all();
    }

    public function saveQuote($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->status = 'quote';
        $order->save();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Order moved to saved quotes.'),
            'alert_type' => 'success'
        ]);
    }

    public function moveToOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->status = 'active';
        $order->save();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Saved quote moved to order.'),
            'alert_type' => 'success'
        ]);
    }

    public function deleteOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->delete();
        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Order deleted.'),
            'alert_type' => 'success'
        ]);
    }

    public function checkoutOrder()
    {
        return view('frontend.learner.self-publishing.order.checkout');
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
            'is_processed' => 1
        ]);

        SelfPublishingOrder::whereIn('id', $currentOrders->pluck('id'))
        ->update([
            'order_id' => $order->id,
            'status' => 'paid'
        ]);

        foreach( $currentOrders as $currentOrder ) {
            $publishingService = PublishingService::find($currentOrder->parent_id);
            
            if ($publishingService->slug === 'sprakvask') {
                CopyEditingManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0
                ]);
            }

            if ($publishingService->slug === 'korrektur') {
                CorrectionManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0
                ]);
            }

            // redaktor
            if ($publishingService->id === 3) {
                SelfPublishing::create([
                    'title' => $currentOrder->title,
                    'description' => $currentOrder->description,
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'manuscript' => $currentOrder->file,
                    'word_count' => $currentOrder->word_count,
                    'price' => $currentOrder->price,
                ]);
            }
        }

        return redirect()->route('learner.self-publishing.order')->with([
            'errors' => AdminHelpers::createMessageBag('Order processed.'),
            'alert_type' => 'success'
        ]);
    }

}
