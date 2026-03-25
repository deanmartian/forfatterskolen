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
use App\ProjectGraphicWork;

class ProjectShopController extends Controller
{
    public function edit($id)
    {
        $layout = str_contains(request()->getHttpHost(), 'giutbok') ? 'giutbok.layout' : 'backend.layout';
        $project = Project::with(['books', 'user'])->findOrFail($id);
        $book = $project->books()->first();

        $genres = [
            'Roman', 'Feelgood', 'Krim', 'Thriller', 'Fantasy', 'Science Fiction',
            'Barnebok', 'Ungdomsbok', 'Poesi', 'Noveller', 'Sakprosa',
            'Biografi', 'Selvhjelp', 'Historisk', 'Kokebok', 'Reise', 'Annet',
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

        $bookName = $book->book_name ?? $project->name;
        $authorName = $project->user->full_name ?? 'Ukjent';

        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            return response()->json(['error' => 'AI-nøkkel ikke konfigurert.'], 500);
        }

        // Hent data fra Graphic Work
        // 1. Baksidetekst fra cover (type=cover, backside_text)
        $coverWork = ProjectGraphicWork::where('project_id', $project->id)
            ->cover()
            ->whereNotNull('backside_text')
            ->orderByDesc('id')
            ->first();

        $backsideText = '';
        if ($coverWork && $coverWork->backside_type === 'text') {
            $backsideText = $coverWork->backside_text;
        }

        // 2. Siste print-ready cover (for bilde-analyse)
        $printReadyCover = ProjectGraphicWork::where('project_id', $project->id)
            ->printReady()
            ->orderByDesc('upload_date')
            ->orderByDesc('id')
            ->first();

        // 3. Bokbilder fra ProjectBookPicture
        $pictures = ProjectBookPicture::where('project_id', $project->id)->get();

        // Bygg AI-prompt
        $content = [];

        // Prøv å legge til cover-bilde fra BookPicture
        foreach ($pictures->take(2) as $pic) {
            $url = $pic->image;
            if (!$url) continue;
            try {
                // Dropbox-filer må gå via intern route
                if (str_contains($url, 'project-')) {
                    $url = url('/dropbox/shared-link/' . trim($url));
                }
                $imageData = @file_get_contents($url);
                if ($imageData && strlen($imageData) < 5000000) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->buffer($imageData);
                    if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                        $content[] = [
                            'type' => 'image',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => $mime,
                                'data' => base64_encode($imageData),
                            ],
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("AI autofill: Kunne ikke hente bilde", ['url' => $url]);
            }
        }

        // Bygg tekstprompt med all tilgjengelig info
        $promptText = "Analyser denne boken og gi meg strukturert informasjon for nettbutikken Indiemoon.no.\n\n";
        $promptText .= "Boktittel: {$bookName}\n";
        $promptText .= "Forfatter: {$authorName}\n\n";

        if ($backsideText) {
            $promptText .= "Baksidetekst fra boken:\n{$backsideText}\n\n";
        }

        if ($printReadyCover) {
            $promptText .= "Print Ready info: format={$printReadyCover->format}, dato={$printReadyCover->upload_date}\n\n";
        }

        $promptText .= "Basert på all tilgjengelig informasjon (baksidetekst, bilder, tittel), svar BARE med JSON:\n";
        $promptText .= '{"genre":"en av: Roman|Feelgood|Krim|Thriller|Fantasy|Science Fiction|Barnebok|Ungdomsbok|Poesi|Noveller|Sakprosa|Biografi|Selvhjelp|Historisk|Kokebok|Reise|Annet",';
        $promptText .= '"target_audience":"en av: voksen|ungdom|barn",';
        $promptText .= '"short_description":"maks 300 tegn, engasjerende norsk salgstekst for bokkort",';
        $promptText .= '"long_description":"baksidetekst for nettbutikken, 2-3 avsnitt på norsk, fang leseren. Bruk baksideteksten som utgangspunkt hvis den finnes."}';

        $content[] = ['type' => 'text', 'text' => $promptText];

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
                return response()->json(['error' => 'AI-forespørsel feilet: ' . $response->status()], 500);
            }

            $text = $response->json('content.0.text', '');

            if (preg_match('/\{[\s\S]*\}/', $text, $match)) {
                $data = json_decode($match[0], true);
                if ($data) {
                    // Legg til kilde-info
                    $data['_sources'] = [];
                    if ($backsideText) $data['_sources'][] = 'baksidetekst';
                    if ($pictures->count() > 0) $data['_sources'][] = 'bokbilder';
                    if (!$backsideText && $pictures->count() === 0) $data['_sources'][] = 'kun tittel og forfatter';
                    return response()->json($data);
                }
            }

            return response()->json(['error' => 'Kunne ikke parse AI-respons.', 'raw' => $text], 500);

        } catch (\Exception $e) {
            Log::error('AI autofill exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'AI-feil: ' . $e->getMessage()], 500);
        }
    }
}
