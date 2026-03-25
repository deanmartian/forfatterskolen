<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\ProjectBook;
use App\Models\AuthorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function books(Request $request)
    {
        $query = ProjectBook::shopVisible()
            ->with(['project:id,user_id', 'project.user:id,first_name,last_name', 'inventory'])
            ->orderBy('shop_sort_order');

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        if ($request->filled('audience')) {
            $query->where('target_audience', $request->audience);
        }

        $books = $query->paginate($request->get('per_page', 24));

        return response()->json([
            'books' => $books->map(fn($b) => $this->formatBook($b)),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    public function featured()
    {
        $books = Cache::remember('shop:featured', 300, function () {
            return ProjectBook::shopVisible()
                ->where('shop_featured', true)
                ->with(['project:id,user_id', 'project.user:id,first_name,last_name'])
                ->orderBy('shop_sort_order')
                ->take(6)
                ->get()
                ->map(fn($b) => $this->formatBook($b));
        });

        return response()->json(['books' => $books]);
    }

    public function show($slug)
    {
        $book = ProjectBook::shopVisible()
            ->where('slug', $slug)
            ->with([
                'project:id,user_id,name',
                'project.user:id,first_name,last_name',
                'project.registrations' => fn($q) => $q->isbns(),
                'inventory',
                'sales',
            ])
            ->firstOrFail();

        $author = $book->project?->user;
        $authorProfile = $author ? AuthorProfile::where('user_id', $author->id)->first() : null;

        // Andre bøker fra samme forfatter
        $otherBooks = $author
            ? ProjectBook::shopVisible()
                ->whereHas('project', fn($q) => $q->where('user_id', $author->id))
                ->where('id', '!=', $book->id)
                ->take(4)
                ->get()
                ->map(fn($b) => $this->formatBook($b))
            : [];

        // ISBN-er
        $isbns = [];
        foreach ($book->project?->registrations ?? [] as $reg) {
            $isbns[$reg->isbn_type] = $reg->value;
        }

        return response()->json([
            'book' => $this->formatBookFull($book, $isbns),
            'author' => $authorProfile ? [
                'name' => $authorProfile->display_name,
                'slug' => $authorProfile->slug,
                'bio' => $authorProfile->bio,
                'photo' => $authorProfile->photo_path,
                'website' => $authorProfile->website,
                'social' => $authorProfile->social_links,
            ] : [
                'name' => $author?->full_name,
                'slug' => null,
                'bio' => null,
                'photo' => null,
                'website' => null,
                'social' => null,
            ],
            'other_books' => $otherBooks,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) {
            return response()->json(['books' => []]);
        }

        $books = ProjectBook::shopVisible()
            ->where(function ($query) use ($q) {
                $query->where('book_name', 'LIKE', "%{$q}%")
                    ->orWhere('short_description', 'LIKE', "%{$q}%")
                    ->orWhere('genre', 'LIKE', "%{$q}%")
                    ->orWhereHas('project.user', function ($uq) use ($q) {
                        $uq->where('first_name', 'LIKE', "%{$q}%")
                            ->orWhere('last_name', 'LIKE', "%{$q}%");
                    });
            })
            ->with(['project:id,user_id', 'project.user:id,first_name,last_name'])
            ->take(20)
            ->get()
            ->map(fn($b) => $this->formatBook($b));

        return response()->json(['books' => $books]);
    }

    public function genres()
    {
        $genres = Cache::remember('shop:genres', 300, function () {
            return ProjectBook::shopVisible()
                ->whereNotNull('genre')
                ->distinct()
                ->pluck('genre')
                ->sort()
                ->values();
        });

        return response()->json(['genres' => $genres]);
    }

    public function author($slug)
    {
        $profile = AuthorProfile::where('slug', $slug)->where('is_visible', true)->firstOrFail();

        $books = ProjectBook::shopVisible()
            ->whereHas('project', fn($q) => $q->where('user_id', $profile->user_id))
            ->with(['project:id,user_id', 'project.user:id,first_name,last_name'])
            ->orderBy('shop_sort_order')
            ->get()
            ->map(fn($b) => $this->formatBook($b));

        return response()->json([
            'author' => [
                'name' => $profile->display_name,
                'slug' => $profile->slug,
                'bio' => $profile->bio,
                'long_bio' => $profile->long_bio,
                'photo' => $profile->photo_path,
                'website' => $profile->website,
                'social' => $profile->social_links,
            ],
            'books' => $books,
        ]);
    }

    private function formatBook(ProjectBook $book): array
    {
        $cover = $book->shop_cover_image;
        if ($cover && str_starts_with($cover, '/Forfatterskolen_app/')) {
            $cover = url('/dropbox/shared-link/' . ltrim($cover, '/'));
        }

        return [
            'id' => $book->id,
            'title' => $book->book_name,
            'slug' => $book->slug,
            'author' => $book->project?->user?->full_name ?? 'Ukjent',
            'cover' => $cover,
            'genre' => $book->genre,
            'price' => $book->price_paperback ?? $book->price_ebook,
            'price_ebook' => $book->price_ebook,
            'formats' => $book->available_formats,
            'featured' => $book->shop_featured,
        ];
    }

    private function formatBookFull(ProjectBook $book, array $isbns): array
    {
        $base = $this->formatBook($book);
        return array_merge($base, [
            'short_description' => $book->short_description,
            'long_description' => $book->long_description,
            'target_audience' => $book->target_audience,
            'categories' => $book->categories,
            'price_hardcover' => $book->price_hardcover,
            'price_audiobook' => $book->price_audiobook,
            'isbns' => $isbns,
            'gallery' => $book->shop_gallery,
            'in_stock' => ($book->inventory?->balance ?? 0) > 0,
            'stock_quantity' => $book->inventory?->balance ?? 0,
        ]);
    }
}
