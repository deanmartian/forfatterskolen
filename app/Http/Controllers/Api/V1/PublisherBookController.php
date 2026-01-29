<?php

namespace App\Http\Controllers\Api\V1;

use App\PublisherBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PublisherBookController extends ApiController
{
    public function index(): JsonResponse
    {
        $books = Cache::remember('api.v1.publisher-books', 3600, function (): array {
            return PublisherBook::query()
                ->orderBy('display_order', 'asc')
                ->get()
                ->map(function (PublisherBook $book): array {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'order' => $book->display_order,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json(['data' => $books]);
    }
}
