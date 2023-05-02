<?php

namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\Http\Controllers\Controller;
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

        SelfPublishingOrder::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'parent' => $request->parent,
            'parent_id' => $request->parent_id,
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

}
