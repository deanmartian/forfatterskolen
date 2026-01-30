<?php

namespace App\Http\Controllers\Api\V1;

use App\PublisherBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PublisherBookController extends ApiController
{
    public function index(): JsonResponse
    {
        $books = Cache::remember('api.v1.publisher-books', 3600, function (): array {
            return PublisherBook::query()
                ->with('libraries')
                ->orderBy('display_order', 'asc')
                ->get()
                ->map(function (PublisherBook $book): array {
                    $primaryLibrary = $book->libraries
                        ->sortBy(fn ($library) => $library->sort_order ?? PHP_INT_MAX)
                        ->first();
                    $bookImage = $primaryLibrary?->book_image ?: $book->book_image;
                    $bookImageLink = $primaryLibrary?->book_link ?: $book->book_image_link;

                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'quote_description' => $book->quote_description,
                        'author_image' => $book->author_image,
                        'book_image' => $this->absoluteUrl($bookImage),
                        'book_image_link' => $bookImageLink,
                        'order' => $book->display_order,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json(['data' => $books]);
    }

    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }
}
