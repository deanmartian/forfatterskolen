<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectBook;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\ProjectBookPicture;

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

    public function aiAutofill($id)
    {
        $project = Project::with(['books', 'user'])->findOrFail($id);
        $book = $project->books()->first();

        if (!$book) {
            return response()->json(['error' => 'Ingen bok funnet.'], 404);
        }

        // Hent bokbilder fra Dropbox
        $pictures = ProjectBookPicture::where('project_id', $project->id)->get();
        $imageUrls = $pictures->pluck('image')->filter()->values()->toArray();

        // Hent boknavn og eksisterende info
        $bookName = $book->book_name ?? $project->name;
        $authorName = $project->user->full_name ?? 'Ukjent';

        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            return response()->json(['error' => 'AI-nøkkel ikke konfigurert.'], 500);
        }

        // Bygg meldinger — med bilder hvis tilgjengelig
        $content = [];

        // Legg til bilder
        foreach (array_slice($imageUrls, 0, 3) as $url) {
            // Prøv å hente bildet
            try {
                $imageData = @file_get_contents($url);
                if ($imageData) {
                    $base64 = base64_encode($imageData);
                    $mime = 'image/jpeg';
                    if (str_contains($url, '.png')) $mime = 'image/png';
                    $content[] = [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $mime,
                            'data' => $base64,
                        ],
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Kunne ikke hente bilde: {$url}");
            }
        }

        $content[] = [
            'type' => 'text',
            'text' => "Analyser denne boken og gi meg strukturert informasjon for en nettbutikk.\n\n"
                . "Boktittel: {$bookName}\n"
                . "Forfatter: {$authorName}\n\n"
                . "Svar BARE med JSON i dette formatet (ingen annen tekst):\n"
                . '{"genre":"en av: Roman|Krim|Thriller|Fantasy|Science Fiction|Barnebok|Ungdomsbok|Poesi|Noveller|Sakprosa|Biografi|Selvhjelp|Kokebok|Reise|Annet",'
                . '"target_audience":"en av: voksen|ungdom|barn",'
                . '"short_description":"maks 300 tegn, engasjerende salgstekst for bokkort",'
                . '"long_description":"baksidetekst, 2-3 avsnitt, fang leseren"}'
        ];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(45)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('AI autofill feil', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['error' => 'AI-forespørsel feilet.'], 500);
            }

            $text = $response->json('content.0.text', '');

            // Ekstraher JSON fra responsen
            if (preg_match('/\{[\s\S]*\}/', $text, $match)) {
                $data = json_decode($match[0], true);
                if ($data) {
                    return response()->json($data);
                }
            }

            return response()->json(['error' => 'Kunne ikke parse AI-respons.'], 500);

        } catch (\Exception $e) {
            Log::error('AI autofill exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'AI-feil: ' . $e->getMessage()], 500);
        }
    }
}
