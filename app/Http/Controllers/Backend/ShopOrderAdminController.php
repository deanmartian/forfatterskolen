<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BookOrder;
use Illuminate\Http\Request;

class ShopOrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = BookOrder::query()->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('fulfillment')) {
            $query->where('fulfillment_status', $request->fulfillment);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('order_number', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->paginate(25);

        $stats = [
            'total_orders' => BookOrder::count(),
            'pending_payment' => BookOrder::where('payment_status', 'pending')->count(),
            'paid_not_shipped' => BookOrder::where('payment_status', 'paid')->where('fulfillment_status', 'pending')->count(),
            'total_revenue' => BookOrder::where('payment_status', 'paid')->sum('total'),
        ];

        return view('backend.shop-orders.index', compact('orders', 'stats'));
    }

    public function show(BookOrder $order)
    {
        return view('backend.shop-orders.show', compact('order'));
    }

    public function ship(BookOrder $order, Request $request)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:50',
        ]);

        $order->update([
            'fulfillment_status' => 'shipped',
            'tracking_number' => $request->tracking_number,
            'shipped_at' => now(),
        ]);

        return back()->with('success', 'Bestilling markert som sendt.');
    }

    public function refund(BookOrder $order)
    {
        $order->update([
            'payment_status' => 'refunded',
        ]);

        return back()->with('success', 'Bestilling markert som refundert.');
    }
}
