<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\BookOrder;

class ShopDownloadController extends Controller
{
    public function download($token)
    {
        $order = BookOrder::where('download_token', $token)
            ->where('payment_status', 'paid')
            ->firstOrFail();

        // Sjekk utløpsdato
        if ($order->download_expires_at && $order->download_expires_at->isPast()) {
            return response()->json(['error' => 'Nedlastingslenken har utløpt.'], 410);
        }

        // Sjekk maks nedlastinger
        $maxDownloads = config('shop.download.max_downloads', 5);
        if ($order->download_count >= $maxDownloads) {
            return response()->json([
                'error' => "Maks antall nedlastinger ({$maxDownloads}) er nådd. Kontakt support.",
            ], 429);
        }

        // Tell opp nedlasting
        $order->increment('download_count');

        // Finn e-bok-fil fra bestillingen
        $ebookItems = collect($order->items)->filter(fn($i) => $i['format'] === 'ebook');

        if ($ebookItems->isEmpty()) {
            return response()->json(['error' => 'Ingen e-bok i denne bestillingen.'], 404);
        }

        // TODO: Returnér faktisk fil-URL fra Dropbox/storage
        // For nå returnerer vi metadata
        return response()->json([
            'download' => [
                'remaining' => $maxDownloads - $order->download_count,
                'expires_at' => $order->download_expires_at?->toIso8601String(),
                'items' => $ebookItems->map(fn($i) => [
                    'title' => $i['title'],
                    'book_id' => $i['book_id'],
                    // 'url' => route('api.shop.download.file', [...]),
                ])->values(),
            ],
        ]);
    }
}
