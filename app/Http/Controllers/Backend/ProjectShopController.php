<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectBook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ProjectShopController extends Controller
{
    public function edit($id)
    {
        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';
        $project = Project::with(['books', 'user'])->findOrFail($id);
        $book = $project->books()->first();

        $genres = [
            'Roman', 'Krim', 'Thriller', 'Fantasy', 'Science Fiction',
            'Barnebok', 'Ungdomsbok', 'Poesi', 'Noveller', 'Sakprosa',
            'Biografi', 'Selvhjelp', 'Kokebok', 'Reise', 'Annet',
        ];

        return view('backend.project.shop', compact('layout', 'project', 'book', 'genres'));
    }

    public function update($id, Request $request)
    {
        $project = Project::findOrFail($id);
        $book = $project->books()->first();

        if (!$book) {
            return back()->with('error', 'Prosjektet har ingen bok registrert.');
        }

        $validated = $request->validate([
            'slug' => 'nullable|string|max:255|unique:project_books,slug,' . $book->id,
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'genre' => 'nullable|string|max:50',
            'target_audience' => 'nullable|string|max:20',
            'price_paperback' => 'nullable|integer|min:0',
            'price_hardcover' => 'nullable|integer|min:0',
            'price_ebook' => 'nullable|integer|min:0',
            'price_audiobook' => 'nullable|integer|min:0',
            'shop_visible' => 'boolean',
            'shop_featured' => 'boolean',
            'shop_sort_order' => 'integer',
            'print_available' => 'boolean',
            'ebook_available' => 'boolean',
            'audiobook_available' => 'boolean',
        ]);

        // Auto-generer slug fra boknavn
        if (empty($validated['slug']) && $book->book_name) {
            $validated['slug'] = Str::slug($book->book_name);
        }

        $validated['shop_visible'] = $request->boolean('shop_visible');
        $validated['shop_featured'] = $request->boolean('shop_featured');
        $validated['print_available'] = $request->boolean('print_available');
        $validated['ebook_available'] = $request->boolean('ebook_available');
        $validated['audiobook_available'] = $request->boolean('audiobook_available');

        $book->update($validated);

        // Tøm shop-cache
        Cache::forget('shop:featured');
        Cache::forget('shop:genres');

        return back()->with('success', 'Nettbutikk-innstillinger oppdatert.');
    }
}
