<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCreative;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdCreativeService
{
    public function getAllCreatives(array $filters = [])
    {
        $query = AdCreative::with(['ads', 'parentCreative']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        return $query->orderByDesc('updated_at')->paginate(20);
    }

    public function generateCreatives(array $brief): array
    {
        $prompt = $this->buildCreativePrompt($brief);

        try {
            $response = $this->callAi($prompt);
            $creatives = $this->parseAiCreativeResponse($response, $brief);

            AdActionLog::log('creatives_generated', [
                'triggered_by' => 'ai',
                'payload' => [
                    'brief' => $brief['product'] ?? 'unknown',
                    'count' => count($creatives),
                ],
            ]);

            return $creatives;
        } catch (\Exception $e) {
            Log::error('AdOS creative generation failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function saveCreative(array $data): AdCreative
    {
        return AdCreative::create($data);
    }

    public function createVariantFrom(int $parentId, array $overrides = []): AdCreative
    {
        $parent = AdCreative::findOrFail($parentId);

        $variant = AdCreative::create(array_merge([
            'platform' => $parent->platform,
            'headlines' => $parent->headlines,
            'descriptions' => $parent->descriptions,
            'primary_text' => $parent->primary_text,
            'cta' => $parent->cta,
            'display_url' => $parent->display_url,
            'final_url' => $parent->final_url,
            'asset_ids' => $parent->asset_ids,
            'variant_of' => $parent->id,
            'generation' => $parent->generation + 1,
            'status' => 'draft',
            'ai_metadata' => ['spawned_from' => $parent->id],
        ], $overrides));

        AdActionLog::log('creative_variant_created', [
            'target_type' => 'creative',
            'target_id' => $variant->id,
            'triggered_by' => 'ai',
            'payload' => ['parent_id' => $parent->id, 'generation' => $variant->generation],
        ]);

        return $variant;
    }

    private function buildCreativePrompt(array $brief): string
    {
        $product = $brief['product'] ?? 'skriveverksted';
        $audience = $brief['audience'] ?? 'voksne som vil skrive bok';
        $goal = $brief['goal'] ?? 'leads';
        $tone = $brief['tone'] ?? 'inspirerende, profesjonell';
        $platform = $brief['platform'] ?? 'facebook';
        $landingPage = $brief['landing_page'] ?? '';
        $proof = $brief['proof'] ?? '';
        $isRetargeting = $brief['retargeting'] ?? false;
        $language = 'norsk';

        return <<<PROMPT
Du er en erfaren annonseskribent for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Lag annonsekreativer for følgende:
- Produkt/tilbud: {$product}
- Målgruppe: {$audience}
- Mål: {$goal}
- Tone: {$tone}
- Plattform: {$platform}
- Landingsside: {$landingPage}
- Sosialt bevis: {$proof}
- Retargeting: {$isRetargeting}
- Språk: {$language}

Returner JSON med denne strukturen:
{
  "variants": [
    {
      "name": "variant-navn",
      "headlines": ["overskrift1", "overskrift2", "overskrift3"],
      "descriptions": ["beskrivelse1", "beskrivelse2"],
      "primary_text": "hovedtekst for annonsen",
      "cta": "Les mer",
      "hook_angle": "kort beskrivelse av vinkelen"
    }
  ]
}

Lag 5 varianter med ulike vinkler:
1. Emosjonell/drøm-vinkel
2. Sosialt bevis / resultat-vinkel
3. Urgency/tilbud-vinkel
4. Problemløsning-vinkel
5. Nysgjerrighet/spørsmål-vinkel

Husk: Norsk språk, pass på at overskrifter er under 30 tegn for Google, under 40 for Facebook.
PROMPT;
    }

    private function callAi(string $prompt): string
    {
        $provider = config('ad_os.ai_provider', 'openai');

        if ($provider === 'openai') {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du er en ekspert på digital annonsering for norske bedrifter. Returner alltid gyldig JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.8,
                'response_format' => ['type' => 'json_object'],
            ]);

            return $response->json('choices.0.message.content', '{}');
        }

        // Anthropic fallback
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-sonnet-4-20250514',
            'max_tokens' => 4096,
            'messages' => [
                ['role' => 'user', 'content' => $prompt . "\n\nReturner gyldig JSON."],
            ],
        ]);

        return $response->json('content.0.text', '{}');
    }

    private function parseAiCreativeResponse(string $response, array $brief): array
    {
        $data = json_decode($response, true);
        if (!$data || !isset($data['variants'])) return [];

        $creatives = [];
        foreach ($data['variants'] as $variant) {
            $creatives[] = AdCreative::create([
                'name' => $variant['name'] ?? 'AI-generert variant',
                'platform' => $brief['platform'] ?? 'universal',
                'headlines' => $variant['headlines'] ?? [],
                'descriptions' => $variant['descriptions'] ?? [],
                'primary_text' => $variant['primary_text'] ?? '',
                'cta' => $variant['cta'] ?? 'Les mer',
                'final_url' => $brief['landing_page'] ?? null,
                'status' => 'draft',
                'ai_metadata' => [
                    'hook_angle' => $variant['hook_angle'] ?? null,
                    'brief' => $brief,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);
        }

        return $creatives;
    }
}
