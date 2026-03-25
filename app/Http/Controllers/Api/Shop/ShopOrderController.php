<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\BookOrder;
use App\ProjectBook;
use App\StorageInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|integer|exists:project_books,id',
            'items.*.format' => 'required|string|in:paperback,hardcover,ebook,audiobook',
            'items.*.quantity' => 'required|integer|min:1|max:10',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:10',
            'shipping_country' => 'nullable|string|size:2',
        ]);

        $hasPhysical = false;
        $subtotal = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $book = ProjectBook::shopVisible()->findOrFail($item['book_id']);

            // Prisberegning
            $price = match ($item['format']) {
                'paperback' => $book->price_paperback,
                'hardcover' => $book->price_hardcover,
                'ebook' => $book->price_ebook,
                'audiobook' => $book->price_audiobook,
                default => null,
            };

            if (!$price) {
                return response()->json([
                    'error' => "«{$book->book_name}» er ikke tilgjengelig som {$item['format']}.",
                ], 422);
            }

            // Lagersjekk for fysiske bøker
            if ($item['format'] !== 'ebook') {
                $hasPhysical = true;
                $inventory = StorageInventory::where('project_book_id', $book->id)->first();
                $available = $inventory?->balance ?? 0;
                if ($available < $item['quantity']) {
                    return response()->json([
                        'error' => "«{$book->book_name}» har kun {$available} eks. på lager.",
                    ], 422);
                }
            }

            $lineTotal = $price * $item['quantity'];
            $subtotal += $lineTotal;

            $orderItems[] = [
                'book_id' => $book->id,
                'title' => $book->book_name,
                'author' => $book->project?->user?->full_name ?? 'Ukjent',
                'format' => $item['format'],
                'quantity' => $item['quantity'],
                'price' => $price,
                'line_total' => $lineTotal,
            ];
        }

        // Frakt
        $country = $validated['shipping_country'] ?? 'NO';
        $shippingCost = 0;
        if ($hasPhysical) {
            $shippingRates = config('shop.shipping');
            $shippingCost = $shippingRates[$country] ?? $shippingRates['default'];
            if ($subtotal >= $shippingRates['free_above']) {
                $shippingCost = 0;
            }
        }

        $total = $subtotal + $shippingCost;

        // E-bok download token
        $hasEbook = collect($orderItems)->contains(fn($i) => $i['format'] === 'ebook');
        $downloadToken = $hasEbook ? Str::random(64) : null;
        $downloadExpires = $hasEbook ? now()->addDays(config('shop.download.expires_days', 30)) : null;

        $order = BookOrder::create([
            'order_number' => BookOrder::generateOrderNumber(),
            'items' => $orderItems,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'shipping_address' => $validated['shipping_address'] ?? null,
            'shipping_city' => $validated['shipping_city'] ?? null,
            'shipping_zip' => $validated['shipping_zip'] ?? null,
            'shipping_country' => $country,
            'download_token' => $downloadToken,
            'download_expires_at' => $downloadExpires,
        ]);

        return response()->json([
            'order' => [
                'order_number' => $order->order_number,
                'total' => $order->total,
                'shipping_cost' => $order->shipping_cost,
                'has_ebook' => $hasEbook,
                'has_physical' => $hasPhysical,
            ],
        ], 201);
    }

    public function show($orderNumber)
    {
        $order = BookOrder::where('order_number', $orderNumber)->firstOrFail();

        return response()->json([
            'order' => [
                'order_number' => $order->order_number,
                'items' => $order->items,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'fulfillment_status' => $order->fulfillment_status,
                'tracking_number' => $order->tracking_number,
                'download_available' => $order->hasEbook() && $order->isPaid(),
                'download_token' => $order->isPaid() ? $order->download_token : null,
            ],
        ]);
    }
}
