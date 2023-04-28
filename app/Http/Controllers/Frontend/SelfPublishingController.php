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

        return view('frontend.learner.self-publishing.order.index', compact('currentOrders', 'currentOrderTotal', 'orderHistory',
            'orderHistoryTotal'));
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
