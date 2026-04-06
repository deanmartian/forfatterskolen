<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DailyWritingNews extends Command
{
    protected $signature = 'community:daily-news {--dry-run : Show without posting}';
    protected $description = 'Generate and post daily writing/publishing news to Community';

    public function handle(): int
    {
        $today = now()->format('l d. F Y');

        $prompt = <<<PROMPT
Du er en redaktør for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Skriv en kort, engasjerende morgenoppdatering for skrivefellesskapet vårt. Inkluder:

1. **Dagens skrivetips** — et praktisk tips for forfattere (variér mellom plott, dialog, karakterer, redigering, motivasjon, etc.)
2. **Bransjenyheter** — 1-2 aktuelle nyheter fra den norske eller internasjonale bokbransjen/forlagsverdenen (oppfinn realistiske men generelle nyheter basert på trender)
3. **Inspirasjon** — et kort sitat fra en kjent forfatter

Hold det kort og uformelt. Maks 200 ord totalt. Bruk emojier sparsomt men naturlig.
Skriv på norsk bokmål. Ikke bruk markdown-overskrifter, bare vanlig tekst med linjeskift.
Dato i dag: {$today}
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 500,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if (!$response->successful()) {
                $this->error('AI API feilet: ' . $response->status());
                Log::error('DailyWritingNews: API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return self::FAILURE;
            }

            $content = $response->json('content.0.text');

            if (!$content) {
                $this->error('Ingen innhold generert');
                return self::FAILURE;
            }

            if ($this->option('dry-run')) {
                $this->info("=== FORHÅNDSVISNING ===\n");
                $this->line($content);
                return self::SUCCESS;
            }

            // Save as draft for approval
            $post = Post::create([
                'id' => Str::uuid(),
                'user_id' => 1376, // Sven Inge (admin)
                'content' => "☀️ God morgen, skrivere!\n\n" . $content,
                'is_bot_post' => true,
                'pinned' => false,
                'status' => 'draft',
            ]);

            // Send email notification for approval
            $approveUrl = config('app.url') . '/admin/community/posts';
            dispatch(new \App\Jobs\AddMailToQueueJob(
                'sven.inge@forfatterskolen.no',
                '📝 Morgennytt klart for godkjenning',
                "Hei!<br><br>Dagens morgennytt for skrivefellesskapet er klart.<br><br>" .
                "<strong>Forhåndsvisning:</strong><br>" . nl2br(e($content)) .
                "<br><br><a href='{$approveUrl}' style='display:inline-block;padding:12px 28px;background:#862736;color:#fff;border-radius:6px;text-decoration:none;font-weight:600;'>Godkjenn og publiser →</a>",
                'post@forfatterskolen.no',
                'Forfatterskolen',
                null, 'daily-news', null
            ));

            $this->info('Morgennytt lagret som utkast — e-post sendt for godkjenning!');
            Log::info('DailyWritingNews: posted successfully');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Feil: ' . $e->getMessage());
            Log::error('DailyWritingNews: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
