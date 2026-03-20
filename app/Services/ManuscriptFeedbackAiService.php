<?php

namespace App\Services;

use App\FreeManuscript;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManuscriptFeedbackAiService
{
    /**
     * Generate AI feedback draft for a free manuscript.
     */
    public function generateFeedback(string $manuscriptContent, string $name, ?string $genre = null): string
    {
        $genreText = $genre ? "Sjangeren er: {$genre}." : '';
        $examples = $this->getTrainingExamples();

        $systemPrompt = <<<PROMPT
Du er en erfaren og varm redaktør ved Forfatterskolen, Norges ledende skriveskole.
Din oppgave er å skrive en profesjonell, varm og oppmuntrende tilbakemelding på en innsendt tekst.

Forfatterskolen tilbyr følgende aktive kurs som du KAN nevne (bruk kun disse, ikke finn på kurs):
{$this->getActiveCourses()}
I tillegg tilbyr Forfatterskolen manusutvikling (profesjonell tilbakemelding på manus).
VIKTIG: Nevn BARE kurs fra listen over. Ikke nevn "Skriveverksted", "Novellekurs" eller andre kurs som ikke finnes i listen.

Tilbakemeldingen skal følge denne strukturen:
1. Takk personen for at de har sendt inn teksten sin.
2. Anerkjenn motet det krever å dele arbeidet sitt.
3. Fremhev styrker i teksten (det som fungerer godt).
4. Pek på forbedringsområder (struktur, språk, karakterer, dialog, spenning, tempo, osv.).
5. Gi spesifikke eksempler fra teksten for å underbygge poengene dine.
6. Oppmuntre personen til å fortsette å skrive.
7. Avslutt med en myk og naturlig omtale av at Forfatterskolens kurs kan hjelpe dem å utvikle seg videre som forfatter.

Retningslinjer:
- Skriv på norsk bokmål.
- Vær varm, oppmuntrende og profesjonell, men ærlig.
- Analyser teksten for: fortellerstemme, karakterutvikling, dialog, struktur, språkbruk, tempo, konflikt og tematikk.
- Gi konkrete eksempler fra den faktiske teksten.
- Tilbakemeldingen skal være 500-800 ord.
- Formater som HTML med <p>-tagger, <strong> for uthevinger og <em> for kursiv.
- IKKE bruk <html>, <head> eller <body>-tagger. Bare innholdet.
- Bruk personens fornavn i tilbakemeldingen.

Her er eksempler på tidligere tilbakemeldinger som viser stilen og tonen vi bruker:

{$examples}

Bruk samme tone, struktur og varme som eksemplene over. Tilpass tilbakemeldingen til den konkrete teksten du får.
PROMPT;

        $userMessage = "Skriv en tilbakemelding på denne teksten.\n\nForfatterens fornavn: {$name}\n{$genreText}\n\nTeksten:\n\n{$manuscriptContent}";

        $apiKey = config('services.anthropic.key');

        if (empty($apiKey)) {
            throw new \Exception('Anthropic API key is not configured.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-opus-4-0-20250514',
            'max_tokens' => 4096,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Feil ved generering av AI-tilbakemelding. Prøv igjen senere.');
        }

        $data = $response->json();

        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Get active courses from database.
     */
    private function getActiveCourses(): string
    {
        $courses = \DB::table('courses')
            ->select('title')
            ->where('status', 1)
            ->where('for_sale', 1)
            ->whereNotIn('id', [43, 108, 113]) // skip free courses
            ->get();

        return $courses->pluck('title')->implode("\n- ");
    }

    /**
     * Get training examples from archived feedback.
     */
    private function getTrainingExamples(): string
    {
        $examples = FreeManuscript::where('is_feedback_sent', 1)
            ->whereNotNull('feedback_content')
            ->where('feedback_content', '!=', '')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $output = '';
        foreach ($examples as $i => $example) {
            $num = $i + 1;
            $manuscriptExcerpt = Str::limit(strip_tags($example->content), 300);
            $feedbackExcerpt = Str::limit(strip_tags(html_entity_decode($example->feedback_content)), 600);
            $output .= "--- EKSEMPEL {$num} ---\n";
            $output .= "Tekst (utdrag): {$manuscriptExcerpt}\n";
            $output .= "Tilbakemelding: {$feedbackExcerpt}\n\n";
        }

        return $output;
    }
}
